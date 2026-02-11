<?php
require_once 'config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
initApiHeaders();

$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Parse the URL to get the product ID if present
$urlParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));
$productId = null;

// Check if there's an ID in the URL (e.g., /api/products/123)
if (count($urlParts) > 2 && $urlParts[count($urlParts) - 1] !== 'products.php') {
    $productId = $urlParts[count($urlParts) - 1];
}

// Also check query parameter
if (!$productId && isset($_GET['id'])) {
    $productId = $_GET['id'];
}

switch ($method) {
    case 'GET':
        if ($productId) {
            // Get single product
            $product = $db->find('products', 'id', $productId);
            if (!$product) {
                sendError('Product not found', 404);
            }
            sendResponse($product);
        }
        else {
            // Get all products
            $products = $db->get('products');
            sendResponse($products);
        }
        break;

    case 'POST':
        // Handle Duplication
        if (isset($_GET['action']) && $_GET['action'] === 'duplicate' && isset($_GET['id'])) {
            $sourceProduct = $db->find('products', 'id', $_GET['id']);
            if (!$sourceProduct) {
                sendError('Source product not found', 404);
            }

            $duplicatedProduct = $sourceProduct;
            $duplicatedProduct['id'] = (string)(time() * 1000);
            $duplicatedProduct['title'] .= ' (Copy)';
            $duplicatedProduct['createdAt'] = date('c');

            $db->push('products', $duplicatedProduct);
            sendResponse($duplicatedProduct);
            break;
        }

        // Add new product
        if (!isset($_FILES['image'])) {
            sendError('No image uploaded', 400);
        }

        $image = $_FILES['image'];
        $imageName = time() . '_' . basename($image['name']);
        $uploadPath = UPLOADS_DIR . $imageName;

        // Create uploads directory if it doesn't exist
        if (!file_exists(UPLOADS_DIR)) {
            mkdir(UPLOADS_DIR, 0755, true);
        }

        if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
            sendError('Failed to upload image', 500);
        }

        $newProduct = [
            'id' => (string)(time() * 1000),
            'title' => $_POST['title'] ?? '',
            'price' => floatval($_POST['price'] ?? 0),
            'shippingPrice' => floatval($_POST['shippingPrice'] ?? 0),
            'category' => $_POST['category'] ?? '',
            'description' => $_POST['description'] ?? '',
            'image' => '/uploads/' . $imageName,
            'createdAt' => date('c')
        ];

        $db->push('products', $newProduct);
        sendResponse($newProduct);
        break;

    case 'PUT':
        // Update product
        if (!$productId) {
            sendError('Product ID required', 400);
        }

        $product = $db->find('products', 'id', $productId);
        if (!$product) {
            sendError('Product not found', 404);
        }

        $data = getRequestData();

        $updatedData = [
            'title' => $data['title'] ?? $product['title'],
            'price' => floatval($data['price'] ?? $product['price']),
            'shippingPrice' => floatval($data['shippingPrice'] ?? $product['shippingPrice']),
            'category' => $data['category'] ?? $product['category'],
            'description' => $data['description'] ?? $product['description']
        ];

        // Check if new image is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $imageName = time() . '_' . basename($image['name']);
            $uploadPath = UPLOADS_DIR . $imageName;

            if (!file_exists(UPLOADS_DIR)) {
                mkdir(UPLOADS_DIR, 0755, true);
            }

            if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
                $updatedData['image'] = '/uploads/' . $imageName;
            }
        }

        $db->update('products', 'id', $productId, $updatedData);
        sendResponse(['message' => 'Product updated successfully']);
        break;

    case 'DELETE':
        // Delete product
        if (!$productId) {
            sendError('Product ID required', 400);
        }

        $db->delete('products', 'id', $productId);
        sendResponse(['message' => 'Product deleted']);
        break;

    default:
        sendError('Method not allowed', 405);
}