<?php
header('Content-Type: text/plain');

echo "Diagnostics Start\n";
echo "PHP Version: " . phpversion() . "\n";

$dataDir = __DIR__ . '/../data';
$dbFile = $dataDir . '/db.json';

echo "Data Directory: $dataDir\n";

if (file_exists($dataDir)) {
    echo "Data Directory exists.\n";
    echo "Is Writable: " . (is_writable($dataDir) ? "YES" : "NO") . "\n";
}
else {
    echo "Data Directory DOES NOT exist.\n";
}

if (file_exists($dbFile)) {
    echo "DB File exists.\n";
    echo "Is Writable: " . (is_writable($dbFile) ? "YES" : "NO") . "\n";
}
else {
    echo "DB File DOES NOT exist.\n";
}

// Try writing
try {
    if (!file_exists($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    $testFile = $dataDir . '/test_write.txt';
    file_put_contents($testFile, 'test');
    echo "Write Test: SUCCESS\n";
    unlink($testFile);
}
catch (Throwable $e) {
    echo "Write Test: FAILED - " . $e->getMessage() . "\n";
}

echo "Diagnostics End\n";
?>