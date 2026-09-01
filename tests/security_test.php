<?php

require_once __DIR__ . '/../includes/security.php';

$failures = [];

function check(bool $condition, string $message): void
{
    global $failures;

    if (!$condition) {
        $failures[] = $message;
    }
}

check(session_has_role(['loggedin' => true, 'role' => 'admin'], 'admin'), 'admin role should authorize admin');
check(!session_has_role(['loggedin' => true, 'role' => 'student'], 'admin'), 'student role must not authorize admin');
check(!session_has_role(['loggedin' => true], 'student'), 'legacy role-less sessions must not authorize');
check(!session_has_role(['loggedin' => false, 'role' => 'admin'], 'admin'), 'logged-out sessions must not authorize');

$token = str_repeat('a', 64);
check(is_valid_csrf_token(['csrf_token' => $token], ['csrf_token' => $token]), 'matching CSRF token should pass');
check(!is_valid_csrf_token(['csrf_token' => $token], ['csrf_token' => str_repeat('b', 64)]), 'mismatched CSRF token must fail');
check(!is_valid_csrf_token([], ['csrf_token' => $token]), 'missing session CSRF token must fail');
check(!is_valid_csrf_token(['csrf_token' => $token], []), 'missing request CSRF token must fail');
check(!is_valid_csrf_token(['csrf_token' => $token], ['csrf_token' => ['invalid']]), 'non-string CSRF token must fail');

$roleRequirements = [
    'admin_dashboard.php' => 'admin',
    'admin_view_student_schedules.php' => 'admin',
    'add_course.php' => 'admin',
    'edit_courses.php' => 'admin',
    'student_dashboard.php' => 'student',
    'view_current_schedule.php' => 'student',
    'addCourse.php' => 'student',
    'dropCourse.php' => 'student',
];

foreach ($roleRequirements as $file => $role) {
    $source = file_get_contents(__DIR__ . '/../' . $file);
    check(str_contains($source, "require_role('$role'"), "$file must require the $role role");
    check(str_contains($source, 'logout_form()'), "$file must use CSRF-protected logout");
}

$csrfHandlers = [
    'admin_authenticate.php',
    'student_authenticate.php',
    'student_createAcc.php',
    'add_course.php',
    'edit_courses.php',
    'addCourse.php',
    'dropCourse.php',
    'logout.php',
];

foreach ($csrfHandlers as $file) {
    $source = file_get_contents(__DIR__ . '/../' . $file);
    check(str_contains($source, 'require_valid_csrf_token();'), "$file must validate a CSRF token");
}

$csrfForms = [
    'admin_login.php',
    'student_login.php',
    'student_createAcc.php',
    'add_course.php',
    'edit_courses.php',
    'addCourse.php',
    'dropCourse.php',
];

foreach ($csrfForms as $file) {
    $source = file_get_contents(__DIR__ . '/../' . $file);
    check(str_contains($source, 'csrf_input()'), "$file must render a CSRF token");
}

$indexSource = file_get_contents(__DIR__ . '/../index.php');
check(!str_contains($indexSource, 'new PDO'), 'public homepage must not open a database connection');

if ($failures !== []) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo "Security tests passed" . PHP_EOL;
