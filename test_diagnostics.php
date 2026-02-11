<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Activiha System Diagnostics</h1>";

// 1. Check PHP Version
echo "<h2>1. PHP Version</h2>";
echo "Current PHP Version: " . phpversion() . "<br>";
if (version_compare(phpversion(), '7.4', '<')) {
    echo "<span style='color:red'>WARNING: PHP version is old. Recommend 7.4 or higher.</span><br>";
}
else {
    echo "<span style='color:green'>OK</span><br>";
}

// 2. Check Directories and Permissions
echo "<h2>2. File System Checks</h2>";
$rootPath = __DIR__;
$dataPath = $rootPath . '/data';
$dbFile = $dataPath . '/db.json';

echo "Web Root: " . $rootPath . "<br>";
echo "Data Directory: " . $dataPath . "<br>";

if (!file_exists($dataPath)) {
    echo "<span style='color:red'>ERROR: 'data' directory missing!</span><br>";
    // Attempt creation
    if (@mkdir($dataPath, 0755, true)) {
        echo "<span style='color:green'>- Attempted to create 'data' directory: SUCCESS</span><br>";
    }
    else {
        echo "<span style='color:red'>- Failed to create 'data' directory. Check permissions.</span><br>";
    }
}
else {
    echo "<span style='color:green'>OK: 'data' directory exists.</span><br>";
    echo "Permissions: " . substr(sprintf('%o', fileperms($dataPath)), -4) . "<br>";
    echo "Writable: " . (is_writable($dataPath) ? "<span style='color:green'>YES</span>" : "<span style='color:red'>NO</span>") . "<br>";
}

// 3. Check Database File
echo "<h2>3. Database File Check</h2>";
if (!file_exists($dbFile)) {
    echo "<span style='color:red'>ERROR: 'db.json' missing!</span><br>";
    // Attempt creation
    if (@file_put_contents($dbFile, json_encode(['status' => 'init']))) {
        echo "<span style='color:green'>- Created test db.json: SUCCESS</span><br>";
    }
    else {
        echo "<span style='color:red'>- Failed to create db.json. Check permissions.</span><br>";
    }
}
else {
    echo "<span style='color:green'>OK: 'db.json' exists.</span><br>";
    echo "Permissions: " . substr(sprintf('%o', fileperms($dbFile)), -4) . "<br>";
    echo "Writable: " . (is_writable($dbFile) ? "<span style='color:green'>YES</span>" : "<span style='color:red'>NO</span>") . "<br>";

    $content = file_get_contents($dbFile);
    $data = json_decode($content, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<span style='color:green'>OK: Valid JSON content.</span><br>";
        echo "Product Count: " . (isset($data['products']) ? count($data['products']) : 0) . "<br>";
    }
    else {
        echo "<span style='color:red'>ERROR: Invalid JSON content.</span><br>";
    }
}

// 4. Test API Configuration Path
echo "<h2>4. API Path Verification</h2>";
$configFile = $rootPath . '/api/config.php';
if (file_exists($configFile)) {
    echo "<span style='color:green'>OK: 'api/config.php' found.</span><br>";
    include $configFile;
    if (defined('DB_FILE')) {
        echo "Resolved DB_FILE constant: " . DB_FILE . "<br>";
        echo "Match check: " . (realpath(DB_FILE) === realpath($dbFile) ? "<span style='color:green'>MATCH</span>" : "<span style='color:red'>MISMATCH</span>") . "<br>";
    }
    else {
        echo "<span style='color:red'>ERROR: DB_FILE constant not defined in config.</span><br>";
    }
}
else {
    echo "<span style='color:red'>ERROR: 'api/config.php' NOT found relative to this test file. Ensure this file is in the root directory.</span><br>";
}

echo "<hr><p>End of Diagnostics</p>";

// 5. API Logic Test (Simulated)
echo "<h2>5. API Logic Test</h2>";
echo "Attempting to load 'demo1' product via internal include...<br>";

// Mock request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/products.php?id=demo1';
$_GET['id'] = 'demo1';

ob_start();
try {
    include $rootPath . '/api/products.php';
    $apiOutput = ob_get_clean();
    echo "<textarea style='width:100%; height:100px;'>" . htmlspecialchars($apiOutput) . "</textarea><br>";
    
    $json = json_decode($apiOutput, true);
    if ($json && (isset($json['id']) || isset($json[0]['id']))) {
        echo "<span style='color:green'>SUCCESS: Retrieved product data from database.</span><br>";
    } else {
        echo "<span style='color:red'>FAILURE: Did not get expected JSON. See output above.</span><br>";
    }
} catch (Exception $e) {
    ob_end_clean();
    echo "<span style='color:red'>CRITICAL ERROR: " . $e->getMessage() . "</span><br>";
}

echo "<hr><p>End of Diagnostics</p>";
?>
