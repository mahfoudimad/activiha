<?php
require_once 'config.php';
initApiHeaders();

$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    sendError('Method not allowed', 405);
}

$data = getRequestData();

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    sendError('Username and password required', 400);
}

$user = null;

// Emergency Bypass for default login during setup
if ($username === 'admin' && $password === 'admin123') {
    $user = ['id' => 1, 'username' => 'admin'];
}
else {
    // Regular check
    $users = $db->get('users');
    foreach ($users as $u) {
        if ($u['username'] === $username) {
            $user = $u;
            break;
        }
    }

    if (!$user || !password_verify($password, $user['password'])) {
        sendError('Invalid credentials', 401);
    }
}

// Create a simple token (in production, use JWT)
$token = base64_encode($username . ':' . time());

sendResponse([
    'token' => $token,
    'user' => [
        'id' => $user['id'],
        'username' => $user['username']
    ]
]);