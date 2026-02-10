<?php
require_once 'config.php';

$db = new Database();
$abandoned = $db->get('abandoned_carts');
$orders = $db->get('orders');
$products = $db->get('products');

$count = 0;
foreach ($abandoned as $cart) {
    // Find product details
    $product = null;
    foreach ($products as $p) {
        if ($p['id'] === $cart['productId']) {
            $product = $p;
            break;
        }
    }

    $newOrder = [
        'id' => $cart['id'],
        'customer' => [
            'fullName' => $cart['fullName'],
            'phone' => $cart['phone'],
            'city' => $cart['city'],
            'address' => ''
        ],
        'product' => [
            'id' => $cart['productId'],
            'title' => $product ? $product['title'] : 'Unknown Product',
            'price' => $product ? $product['price'] : 0
        ],
        'quantity' => 1,
        'totalPrice' => $product ? $product['price'] : 0,
        'status' => 'abandoned',
        'createdAt' => $cart['createdAt'],
        'updatedAt' => $cart['updatedAt']
    ];

    $orders[] = $newOrder;
    $count++;
}

if ($count > 0) {
    $db->set('orders', $orders);
    // Clear abandoned_carts
    $db->set('abandoned_carts', []);
    echo "Successfully migrated $count abandoned carts to orders.";
}
else {
    echo "No abandoned carts found to migrate.";
}