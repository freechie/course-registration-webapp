<?php

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_only_cookies', '1');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8')
        . '">';
}

function logout_form(): string
{
    return '<form method="post" action="logout.php" style="display:inline">'
        . csrf_input()
        . '<button type="submit" class="btn btn-dark">Log out</button>'
        . '</form>';
}

function is_valid_csrf_token(array $session, array $request): bool
{
    $expected = $session['csrf_token'] ?? null;
    $provided = $request['csrf_token'] ?? null;

    return is_string($expected)
        && is_string($provided)
        && hash_equals($expected, $provided);
}

function require_valid_csrf_token(): void
{
    if (!is_valid_csrf_token($_SESSION, $_POST)) {
        http_response_code(403);
        exit('Invalid request token.');
    }
}

function session_has_role(array $session, string $role): bool
{
    return ($session['loggedin'] ?? false) === true
        && ($session['role'] ?? null) === $role;
}

function require_role(string $role, string $loginPage): void
{
    if (!session_has_role($_SESSION, $role)) {
        header("Location: $loginPage");
        exit;
    }
}

function establish_authenticated_session(string $role, array $identity): void
{
    session_regenerate_id(true);
    $_SESSION = array_merge([
        'loggedin' => true,
        'role' => $role,
        'csrf_token' => bin2hex(random_bytes(32)),
    ], $identity);
}
