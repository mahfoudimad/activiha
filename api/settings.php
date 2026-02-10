<?php
require_once 'config.php';
initApiHeaders();

$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get form settings (return directly, not nested)
        $formSettings = $db->get('formSettings');
        sendResponse($formSettings);
        break;

    case 'PUT':
        // Update settings
        $data = getRequestData();
        $db->set('formSettings', $data);
        sendResponse(['message' => 'Settings updated successfully']);
        break;

    default:
        sendError('Method not allowed', 405);
}