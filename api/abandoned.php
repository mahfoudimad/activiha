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

        // Check if we already have this phone for this product
        $carts = $db->get('abandoned_carts');
        $foundIndex = -1;
        foreach ($carts as $index => $cart) {
            if ($cart['phone'] === $phone && $cart['productId'] === $productId) {
                $foundIndex = $index;
                break;
            }
        }

        $fullName = $data['fullName'] ?? '';
        $city = $data['city'] ?? '';

        if ($foundIndex >= 0) {
            // Update existing
            $carts[$foundIndex]['fullName'] = $fullName;
            $carts[$foundIndex]['city'] = $city;
            $carts[$foundIndex]['updatedAt'] = date('c');
            $db->save('abandoned_carts', $carts);
            sendResponse(['message' => 'Updated']);
        }
        else {
            // Create new
            $newCart = [
                'id' => (string)(time() * 1000),
                'phone' => $phone,
                'fullName' => $fullName,
                'city' => $city,
                'productId' => $productId,
                'createdAt' => date('c'),
                'updatedAt' => date('c'),
                'status' => 'abandoned'
            ];
            $db->push('abandoned_carts', $newCart);
            sendResponse($newCart);
        }
        break;

    default:
        sendError('Method not allowed', 405);
}