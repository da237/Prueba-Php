<?php
require '../config/database.php';
require __DIR__ . '/../src/ProductoController.php';

// Cabeceras CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Manejo de preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$controller = new ProductController($pdo);
$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

if ($path[0] === 'products') {
    $id = $path[1] ?? null;

    switch ($method) {
        case 'GET':
            echo json_encode($controller->getAll());
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->create($data));
            break;

        case 'PUT':
            if (!$id || !is_numeric($id)) {
                http_response_code(400);
                echo json_encode(['error' => 'ID inválido']);
                break;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->update($id, $data));
            break;

        case 'DELETE':
            if (!$id || !is_numeric($id)) {
                http_response_code(400);
                echo json_encode(['error' => 'ID inválido']);
                break;
            }
            echo json_encode($controller->delete($id));
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}