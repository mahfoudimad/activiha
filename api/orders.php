<?php
require_once 'config.php';
initApiHeaders();

$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Parse the URL to get the order ID if present
$urlParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));
$orderId = null;

// Check if there's an ID in the URL
if (count($urlParts) > 2 && $urlParts[count($urlParts) - 1] !== 'orders.php') {
    $orderId = $urlParts[count($urlParts) - 1];
}

// Also check query parameter
if (!$orderId && isset($_GET['id'])) {
    $orderId = $_GET['id'];
}

switch ($method) {
    case 'GET':
        // Get all orders (for admin)
        $orders = $db->get('orders');

        // Sort by createdAt descending
        usort($orders, function ($a, $b) {
            return strtotime($b['createdAt']) - strtotime($a['createdAt']);
        });

        sendResponse($orders);
        break;

    case 'POST':
        // Place new order
        $data = getRequestData();

        $fullName = $data['fullName'] ?? '';
        $phone = $data['phone'] ?? '';
        $city = $data['city'] ?? '';
        $address = $data['address'] ?? '';
        $productId = $data['productId'] ?? '';
        $quantity = intval($data['quantity'] ?? 1);
        $totalPrice = floatval($data['totalPrice'] ?? 0);

        // Get product details
        $product = $db->find('products', 'id', $productId);

        $newOrder = [
            'id' => (string)(time() * 1000),
            'customer' => [
                'fullName' => $fullName,
                'phone' => $phone,
                'city' => $city,
                'address' => $address
            ],
            'product' => [
                'id' => $productId,
                'title' => $product ? $product['title'] : 'Unknown Product',
                'price' => $product ? $product['price'] : 0
            ],
            'quantity' => $quantity,
            'totalPrice' => $totalPrice,
            'status' => 'pending', // pending, confirmed, delivered, canceled
            'createdAt' => date('c')
        ];

        $db->push('orders', $newOrder);

        // Sync to third-party integrations
        require_once 'integrations.php';
        $settings = $db->get('settings');
        syncToWooCommerce($newOrder, $settings);
        syncToGoogleSheets($newOrder, $settings);

        sendResponse($newOrder);
        break;

    case 'PATCH':
        // Update order status
        if (!$orderId) {
            sendError('Order ID required', 400);
        }

        $data = getRequestData();
        $status = $data['status'] ?? '';

        $db->update('orders', 'id', $orderId, ['status' => $status]);
        sendResponse(['message' => 'Order updated']);
        break;

    case 'PUT':
        // Update full order (Admin)
        if (!$orderId) {
            sendError('Order ID required', 400);
        }

        $order = $db->find('orders', 'id', $orderId);
        if (!$order) {
            sendError('Order not found', 404);
        }

        $data = getRequestData();

        $updatedData = [
            'customer' => [
                'fullName' => $data['fullName'] ?? '',
                'phone' => $data['phone'] ?? '',
                'city' => $data['city'] ?? '',
                'address' => $data['address'] ?? ''
            ],
            'quantity' => intval($data['quantity'] ?? 1),
            'totalPrice' => floatval($data['totalPrice'] ?? 0),
            'status' => $data['status'] ?? 'pending'
        ];

        $db->update('orders', 'id', $orderId, $updatedData);
        sendResponse(['message' => 'Order updated successfully']);
        break;

    default:
        sendError('Method not allowed', 405);
}