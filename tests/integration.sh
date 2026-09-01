#!/usr/bin/env bash
set -euo pipefail

repo_root=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
compose_file="$repo_root/compose.test.yml"
project_name="course-registration-test-$$"
test_dir=$(mktemp -d "${TMPDIR:-/tmp}/course-registration-test.XXXXXX")
php_pid=''
export COMPOSE_PROGRESS=plain

cleanup() {
    if [[ -n "$php_pid" ]]; then
        kill "$php_pid" 2>/dev/null || true
        wait "$php_pid" 2>/dev/null || true
    fi

    docker compose -p "$project_name" -f "$compose_file" down --volumes --remove-orphans >/dev/null 2>&1 || true
    rm -rf -- "$test_dir"
}
trap cleanup EXIT

request() {
    local name=$1
    shift
    response_status=$(curl --silent --show-error \
        --dump-header "$test_dir/$name.headers" \
        --output "$test_dir/$name.body" \
        --write-out '%{http_code}' \
        "$@")
}

assert_status() {
    local expected=$1
    local name=$2

    if [[ "$response_status" != "$expected" ]]; then
        echo "$name returned $response_status, expected $expected" >&2
        sed -n '1,20p' "$test_dir/$name.body" >&2
        exit 1
    fi
}

assert_body_contains() {
    local name=$1
    local expected=$2

    if ! grep -Fq "$expected" "$test_dir/$name.body"; then
        echo "$name response did not contain: $expected" >&2
        exit 1
    fi
}

assert_location() {
    local name=$1
    local expected=$2

    if ! tr -d '\r' < "$test_dir/$name.headers" | grep -Fqx "Location: $expected"; then
        echo "$name did not redirect to $expected" >&2
        sed -n '1,20p' "$test_dir/$name.headers" >&2
        exit 1
    fi
}

csrf_from() {
    local name=$1
    sed -n 's/.*name="csrf_token" value="\([^"]*\)".*/\1/p' "$test_dir/$name.body" | head -n 1
}

db_scalar() {
    docker compose -p "$project_name" -f "$compose_file" exec -T db \
        mysql --batch --skip-column-names -uroot course_registration -e "$1"
}

cd "$repo_root"
docker compose -p "$project_name" -f "$compose_file" up --detach --wait

db_binding=$(docker compose -p "$project_name" -f "$compose_file" port db 3306)
db_port=${db_binding##*:}

docker compose -p "$project_name" -f "$compose_file" exec -T db mysql -uroot < database/schema.sql

export DB_HOST=127.0.0.1
export DB_PORT="$db_port"
export DB_USER=root
export DB_PASSWORD=''
export DB_NAME=course_registration

php tests/seed.php

app_port=18080
php -S "127.0.0.1:$app_port" >"$test_dir/php-server.log" 2>&1 &
php_pid=$!
base_url="http://127.0.0.1:$app_port"

for _ in {1..30}; do
    if curl --silent --fail "$base_url/index.php" >/dev/null 2>&1; then
        break
    fi
    sleep 0.2
done

admin_cookies="$test_dir/admin.cookies"
request admin_login --cookie-jar "$admin_cookies" "$base_url/admin_login.php"
assert_status 200 admin_login
admin_token=$(csrf_from admin_login)

request admin_auth \
    --cookie "$admin_cookies" --cookie-jar "$admin_cookies" \
    --request POST \
    --data-urlencode "csrf_token=$admin_token" \
    --data-urlencode 'admin_username=admin_example' \
    --data-urlencode 'admin_password=Admin Test Password' \
    "$base_url/admin_authenticate.php"
assert_status 302 admin_auth
assert_location admin_auth 'admin_dashboard.php'

request admin_dashboard --cookie "$admin_cookies" "$base_url/admin_dashboard.php"
assert_status 200 admin_dashboard
assert_body_contains admin_dashboard 'Welcome, admin_example'

request add_course_form --cookie "$admin_cookies" "$base_url/add_course.php"
assert_status 200 add_course_form
admin_token=$(csrf_from add_course_form)

request add_course \
    --cookie "$admin_cookies" \
    --request POST \
    --data-urlencode "csrf_token=$admin_token" \
    --data-urlencode 'cid=103' \
    --data-urlencode 'cname=Integration Testing' \
    --data-urlencode 'instructor=Prof. Test' \
    --data-urlencode 'day=Fri' \
    --data-urlencode 'time=PM' \
    "$base_url/add_course.php"
assert_status 200 add_course
assert_body_contains add_course 'Course added successfully!'
[[ $(db_scalar 'SELECT COUNT(*) FROM courses WHERE cid = 103;') == 1 ]]

request forged_admin_change \
    --cookie "$admin_cookies" \
    --request POST \
    --data-urlencode 'cid=104' \
    --data-urlencode 'cname=Forged Course' \
    --data-urlencode 'instructor=Nobody' \
    --data-urlencode 'day=Fri' \
    --data-urlencode 'time=AM' \
    "$base_url/add_course.php"
assert_status 403 forged_admin_change
[[ $(db_scalar 'SELECT COUNT(*) FROM courses WHERE cid = 104;') == 0 ]]

request edit_courses_form --cookie "$admin_cookies" "$base_url/edit_courses.php"
assert_status 200 edit_courses_form
admin_token=$(csrf_from edit_courses_form)

request remove_course \
    --cookie "$admin_cookies" \
    --request POST \
    --data-urlencode "csrf_token=$admin_token" \
    --data-urlencode 'cid=103' \
    "$base_url/edit_courses.php"
assert_status 200 remove_course
assert_body_contains remove_course 'Course removed successfully!'
[[ $(db_scalar 'SELECT COUNT(*) FROM courses WHERE cid = 103;') == 0 ]]

student_cookies="$test_dir/student.cookies"
request registration --cookie-jar "$student_cookies" "$base_url/student_createAcc.php"
assert_status 200 registration
student_token=$(csrf_from registration)

request register_student \
    --cookie "$student_cookies" --cookie-jar "$student_cookies" \
    --request POST \
    --data-urlencode "csrf_token=$student_token" \
    --data-urlencode 'sid=900002' \
    --data-urlencode 'sname=Taylor Test' \
    --data-urlencode 'username=taylor_test' \
    --data-urlencode 'password=Student Test Password' \
    --data-urlencode 'confirm_password=Student Test Password' \
    "$base_url/student_createAcc.php"
assert_status 302 register_student
assert_location register_student 'student_login.php?registered=1'
[[ $(db_scalar "SELECT COUNT(*) FROM students WHERE username = 'taylor_test';") == 1 ]]

request student_login --cookie "$student_cookies" --cookie-jar "$student_cookies" "$base_url/student_login.php"
assert_status 200 student_login
student_token=$(csrf_from student_login)

request student_auth \
    --cookie "$student_cookies" --cookie-jar "$student_cookies" \
    --request POST \
    --data-urlencode "csrf_token=$student_token" \
    --data-urlencode 'username=taylor_test' \
    --data-urlencode 'password=Student Test Password' \
    "$base_url/student_authenticate.php"
assert_status 302 student_auth
assert_location student_auth 'student_dashboard.php'

request student_as_admin --cookie "$student_cookies" "$base_url/admin_dashboard.php"
assert_status 302 student_as_admin
assert_location student_as_admin 'admin_login.php'

request add_enrollment_form --cookie "$student_cookies" "$base_url/addCourse.php"
assert_status 200 add_enrollment_form
student_token=$(csrf_from add_enrollment_form)

request add_enrollment \
    --cookie "$student_cookies" \
    --request POST \
    --data-urlencode "csrf_token=$student_token" \
    --data-urlencode 'cid=102' \
    "$base_url/addCourse.php"
assert_status 200 add_enrollment
assert_body_contains add_enrollment 'Course added successfully.'
[[ $(db_scalar 'SELECT COUNT(*) FROM enrolled WHERE sid = 900002 AND cid = 102;') == 1 ]]

request drop_enrollment_form --cookie "$student_cookies" "$base_url/dropCourse.php"
assert_status 200 drop_enrollment_form
student_token=$(csrf_from drop_enrollment_form)

request drop_enrollment \
    --cookie "$student_cookies" \
    --request POST \
    --data-urlencode "csrf_token=$student_token" \
    --data-urlencode 'cid=102' \
    "$base_url/dropCourse.php"
assert_status 200 drop_enrollment
assert_body_contains drop_enrollment 'Course dropped successfully.'
[[ $(db_scalar 'SELECT COUNT(*) FROM enrolled WHERE sid = 900002 AND cid = 102;') == 0 ]]

request logout_get --cookie "$student_cookies" "$base_url/logout.php"
assert_status 405 logout_get

request student_dashboard --cookie "$student_cookies" "$base_url/student_dashboard.php"
assert_status 200 student_dashboard
student_token=$(csrf_from student_dashboard)

request logout_post \
    --cookie "$student_cookies" --cookie-jar "$student_cookies" \
    --request POST \
    --data-urlencode "csrf_token=$student_token" \
    "$base_url/logout.php"
assert_status 302 logout_post
assert_location logout_post 'index.php'

request after_logout --cookie "$student_cookies" "$base_url/student_dashboard.php"
assert_status 302 after_logout
assert_location after_logout 'student_login.php'

echo "Database integration tests passed"
