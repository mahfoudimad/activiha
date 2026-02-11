<?php
// Router for PHP built-in server to simulate .htaccess

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// API Routes
if (preg_match('#^/api/products/([^/]+)$#', $uri, $matches)) {
    $_GET['id'] = $matches[1];
    require 'api/products.php';
    return;
}
if ($uri === '/api/products') {
    require 'api/products.php';
    return;
}

if (preg_match('#^/api/orders/([^/]+)$#', $uri, $matches)) {
    $_GET['id'] = $matches[1];
    require 'api/orders.php';
    return;
}
if ($uri === '/api/orders') {
    require 'api/orders.php';
    return;
}

if (preg_match('#^/api/pages/([^/]+)$#', $uri, $matches)) {
    $_GET['id'] = $matches[1];
    require 'api/pages.php';
    return;
}
if ($uri === '/api/pages') {
    require 'api/pages.php';
    return;
}

if ($uri === '/api/auth/login' || $uri === '/api/auth') {
    require 'api/auth.php';
    return;
}

if ($uri === '/api/settings') {
    require 'api/settings.php';
    return;
}
if ($uri === '/api/stats') {
    require 'api/stats.php';
    return;
}

// Frontend Routes
if (preg_match('#^/product/([^/]+)$#', $uri, $matches)) {
    $_GET['id'] = $matches[1];
    require 'product.php';
    return;
}

if (preg_match('#^/page/([^/]+)$#', $uri, $matches)) {
    $_GET['id'] = $matches[1];
    require 'page.php';
    return;
}

// Admin Route
if (strpos($uri, '/admin') === 0) {
    if ($uri === '/admin' || $uri === '/admin/') {
        require 'admin/index.html';
        return;
    }
}

// Default to index.php
require 'index.php';