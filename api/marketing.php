<?php
require_once 'config.php';
initApiHeaders();

$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $marketing = $db->get('marketing');
        sendResponse($marketing ?: [
            'fbPixel' => '',
            'tiktokPixel' => '',
            'googleAnalytics' => '',
            'googleSearchConsole' => ''
        ]);
        break;

    case 'PUT':
        $data = getRequestData();
        $db->set('marketing', $data);
        sendResponse(['message' => 'Marketing settings updated']);
        break;

    default:
        sendError('Method not allowed', 405);
}