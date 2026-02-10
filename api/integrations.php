<?php

function syncToWooCommerce($order, $settings)
{
    if (empty($settings['integrations']['woocommerce']['url']))
        return;

    $url = rtrim($settings['integrations']['woocommerce']['url'], '/') . '/wp-json/wc/v3/orders';
    $key = $settings['integrations']['woocommerce']['key'];
    $secret = $settings['integrations']['woocommerce']['secret'];

    $data = [
        'payment_method' => 'cod',
        'payment_method_title' => 'Cash on Delivery (Activiha)',
        'set_paid' => false,
        'billing' => [
            'first_name' => $order['customer']['fullName'],
            'phone' => $order['customer']['phone'],
            'city' => $order['customer']['city'],
            'address_1' => $order['customer']['address'],
            'country' => 'DZ'
        ],
        'line_items' => [
            [
                'name' => $order['product']['title'],
                'quantity' => $order['quantity'],
                'total' => (string)$order['totalPrice']
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_USERPWD, "$key:$secret");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_exec($ch);
    curl_close($ch);
}

function syncToGoogleSheets($order, $settings)
{
    if (empty($settings['integrations']['googlesheets']['url']))
        return;

    $url = $settings['integrations']['googlesheets']['url'];

    $data = [
        'id' => $order['id'],
        'fullName' => $order['customer']['fullName'],
        'phone' => $order['customer']['phone'],
        'city' => $order['customer']['city'],
        'address' => $order['customer']['address'],
        'product' => $order['product']['title'],
        'quantity' => $order['quantity'],
        'total' => $order['totalPrice'],
        'date' => $order['createdAt']
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_exec($ch);
    curl_close($ch);
}