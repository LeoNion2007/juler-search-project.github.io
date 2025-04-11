<?php
header('Content-Type: application/json');

// Путь к файлу с данными
$dataFile = 'school.mos.ru.txt';

// Проверяем, существует ли файл
if (!file_exists($dataFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Database file not found']);
    exit;
}

// Получаем поисковый запрос из URL
$requestUri = $_SERVER['REQUEST_URI'];
$searchQuery = trim(parse_url($requestUri, PHP_URL_PATH), '/');
$searchQuery = str_replace('search.php/', '', $searchQuery);

if (empty($searchQuery)) {
    http_response_code(400);
    echo json_encode(['error' => 'Search query is empty']);
    exit;
}

// Читаем файл с данными
$lines = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($lines === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to read database file']);
    exit;
}

// Извлекаем заголовки (первая строка)
$headers = str_getcsv(array_shift($lines));

// Подготавливаем результаты поиска
$results = [];

foreach ($lines as $line) {
    $data = str_getcsv($line);
    $combinedData = array_combine($headers, $data);
    
    // Ищем во всех полях
    foreach ($combinedData as $value) {
        if (stripos($value, $searchQuery) !== false) {
            $results[] = $combinedData;
            break;
        }
    }
}

// Возвращаем результаты
if (!empty($results)) {
    echo json_encode($results);
} else {
    http_response_code(404);
    echo json_encode(['message' => 'No results found']);
}
?>