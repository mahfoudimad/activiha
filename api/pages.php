<?php
require_once 'config.php';
initApiHeaders();

$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Parse the URL to get the page ID if present
$urlParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));
$pageId = null;

if (count($urlParts) > 2 && $urlParts[count($urlParts) - 1] !== 'pages.php') {
    $pageId = $urlParts[count($urlParts) - 1];
}

if (!$pageId && isset($_GET['id'])) {
    $pageId = $_GET['id'];
}

switch ($method) {
    case 'GET':
        if ($pageId) {
            // Get single page
            $page = $db->find('pages', 'id', $pageId);
            if (!$page) {
                sendError('Page not found', 404);
            }
            sendResponse($page);
        }
        else {
            // Get all pages
            $pages = $db->get('pages');
            sendResponse($pages);
        }
        break;

    case 'POST':
        // Create new page
        $data = getRequestData();

        $newPage = [
            'id' => (string)(time() * 1000),
            'title' => $data['title'] ?? '',
            'content' => $data['content'] ?? '',
            'slug' => $data['slug'] ?? '',
            'createdAt' => date('c')
        ];

        $db->push('pages', $newPage);
        sendResponse($newPage);
        break;

    case 'PUT':
        // Update page
        if (!$pageId) {
            sendError('Page ID required', 400);
        }

        $data = getRequestData();

        $updatedData = [
            'title' => $data['title'] ?? '',
            'content' => $data['content'] ?? '',
            'slug' => $data['slug'] ?? ''
        ];

        $db->update('pages', 'id', $pageId, $updatedData);
        sendResponse(['message' => 'Page updated successfully']);
        break;

    case 'DELETE':
        // Delete page
        if (!$pageId) {
            sendError('Page ID required', 400);
        }

        $db->delete('pages', 'id', $pageId);
        sendResponse(['message' => 'Page deleted']);
        break;

    default:
        sendError('Method not allowed', 405);
}