<?php
// Database configuration using JSON file storage (similar to lowdb)
define('DB_FILE', __DIR__ . '/../data/db.json');
define('UPLOADS_DIR', __DIR__ . '/../uploads/');

// Help prevent accidental raw output if includes fail
error_reporting(0);

function initApiHeaders()
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

// Database helper functions
class Database
{
    private $dbFile;
    private $data;

    public function __construct()
    {
        $this->dbFile = DB_FILE;
        $this->load();
    }

    private function load()
    {
        if (!file_exists($this->dbFile)) {
            // Create default database structure
            $this->data = [
                'products' => [],
                'orders' => [],
                'pages' => [],
                'users' => [
                    [
                        'id' => 1,
                        'username' => 'admin',
                        'password' => password_hash('admin123', PASSWORD_BCRYPT)
                    ]
                ],
                'settings' => [
                    'storeName' => 'Activiha',
                    'contactWhatsapp' => '+213000000000',
                    'currency' => 'DZD'
                ],
                'formSettings' => [
                    'language' => 'ar',
                    'themeColor' => '#2563eb',
                    'headerColor' => '#f97316',
                    'customLabels' => [
                        'mainCta' => [
                            'ar' => 'أطلب الآن - الدفع عند الاستلام',
                            'fr' => 'Commander Maintenant - COD'
                        ],
                        'fullName' => [
                            'ar' => 'الإسم الكامل',
                            'fr' => 'Nom Complet'
                        ],
                        'phone' => [
                            'ar' => 'رقم الهاتف',
                            'fr' => 'Numéro de Téléphone'
                        ],
                        'wilaya' => [
                            'ar' => 'الولاية',
                            'fr' => 'Wilaya'
                        ],
                        'city' => [
                            'ar' => 'البلدية / المدينة',
                            'fr' => 'Commune / Ville'
                        ],
                        'total' => [
                            'ar' => 'المجموع النهائي',
                            'fr' => 'Total Final'
                        ]
                    ],
                    'fieldVisibility' => [
                        'fullName' => true,
                        'phone' => true,
                        'wilaya' => true,
                        'city' => true,
                        'quantity' => true
                    ]
                ],
                'stats' => [
                    'pageViews' => 0
                ],
                'marketing' => [
                    'fbPixel' => '',
                    'tiktokPixel' => '',
                    'googleAnalytics' => '',
                    'googleSearchConsole' => ''
                ]
            ];
            $this->save();
        }
        else {
            $this->data = json_decode(file_get_contents($this->dbFile), true);
        }
    }

    public function save()
    {
        $dir = dirname($this->dbFile);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->dbFile, json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function get($collection)
    {
        return isset($this->data[$collection]) ? $this->data[$collection] : [];
    }

    public function set($collection, $data)
    {
        $this->data[$collection] = $data;
        $this->save();
    }

    public function push($collection, $item)
    {
        if (!isset($this->data[$collection])) {
            $this->data[$collection] = [];
        }
        $this->data[$collection][] = $item;
        $this->save();
        return $item;
    }

    public function find($collection, $key, $value)
    {
        if (!isset($this->data[$collection])) {
            return null;
        }
        foreach ($this->data[$collection] as $item) {
            if (isset($item[$key]) && $item[$key] == $value) {
                return $item;
            }
        }
        return null;
    }

    public function update($collection, $key, $value, $newData)
    {
        if (!isset($this->data[$collection])) {
            return false;
        }
        foreach ($this->data[$collection] as $index => $item) {
            if (isset($item[$key]) && $item[$key] == $value) {
                $this->data[$collection][$index] = array_merge($item, $newData);
                $this->save();
                return true;
            }
        }
        return false;
    }

    public function delete($collection, $key, $value)
    {
        if (!isset($this->data[$collection])) {
            return false;
        }
        foreach ($this->data[$collection] as $index => $item) {
            if (isset($item[$key]) && $item[$key] == $value) {
                array_splice($this->data[$collection], $index, 1);
                $this->save();
                return true;
            }
        }
        return false;
    }
}

error_reporting(0);

function sendResponse($data, $statusCode = 200)
{
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

function sendError($message, $statusCode = 400)
{
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    http_response_code($statusCode);
    echo json_encode(['error' => $message, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit();
}

function getRequestData()
{
    $input = file_get_contents('php://input');
    if (!empty($input)) {
        $data = json_decode($input, true);
        if (is_array($data))
            return $data;
    }

    return array_merge($_POST, $_GET);
}