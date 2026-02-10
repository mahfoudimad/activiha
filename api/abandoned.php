<?php
require_once 'config.php';
initApiHeaders();

$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Only admin can see abandoned carts
        $carts = $db->get('abandoned_carts');
        // Sort by date desc
        usort($carts, function ($a, $b) {
            return strtotime($b['updatedAt']) - strtotime($a['updatedAt']);
        });
        sendResponse($carts);
        break;

    case 'POST':
        $data = getRequestData();
        $phone = $data['phone'] ?? '';
        $productId = $data['productId'] ?? '';

        if (empty($phone) || empty($productId)) {
            sendError('Phone and Product ID required', 400);
        }

        $fullName = $data['fullName'] ?? '';
        $city = $data['city'] ?? '';

        // Check if we already have an abandoned order for this phone and product
        $orders = $db->get('orders');
        $foundIndex = -1;
        foreach ($orders as $index => $order) {
            if ($order['status'] === 'abandoned' &&
            $order['customer']['phone'] === $phone &&
            $order['product']['id'] === $productId) {
                $foundIndex = $index;
                break;
            }
        }

        // Get product details for title/price
        $product = $db->find('products', 'id', $productId);

        if ($foundIndex >= 0) {
            // Update existing abandoned order
            $orders[$foundIndex]['customer']['fullName'] = $fullName;
            $orders[$foundIndex]['customer']['city'] = $city;
            $orders[$foundIndex]['updatedAt'] = date('c');
            $db->set('orders', $orders);
            sendResponse(['message' => 'Abandoned order updated']);
        }
        else {
            // Create new abandoned order
            $newOrder = [
                'id' => (string)(time() * 1000),
                'customer' => [
                    'fullName' => $fullName,
                    'phone' => $phone,
                    'city' => $city,
                    'address' => ''
                ],
                'product' => [
                    'id' => $productId,
                    'title' => $product ? $product['title'] : 'Unknown Product',
                    'price' => $product ? $product['price'] : 0
                ],
                'quantity' => 1,
                'totalPrice' => $product ? $product['price'] : 0,
                'status' => 'abandoned',
                'createdAt' => date('c'),
                'updatedAt' => date('c')
            ];
            $db->push('orders', $newOrder);
            sendResponse($newOrder);
        }
        break;

    default:
        sendError('Method not allowed', 405);
}