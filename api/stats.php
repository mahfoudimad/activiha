<?php
require_once 'config.php';
initApiHeaders();

$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stats = $db->get('stats');
        if (!$stats) {
            $stats = ['pageViews' => 0];
            $db->save('stats', $stats);
        }
        sendResponse($stats);
        break;

    case 'POST':
        $stats = $db->get('stats');
        if (!$stats) {
            $stats = ['pageViews' => 0];
        }
        $stats['pageViews']++;
        $db->save('stats', $stats);
        sendResponse(['message' => 'Views incremented', 'pageViews' => $stats['pageViews']]);
        break;

    default:
        sendError('Method not allowed', 405);
}