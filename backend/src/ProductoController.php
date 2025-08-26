<?php
class ProductController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        if (empty($data['name']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            http_response_code(400);
            return ['error' => 'Nombre y precio válidos son requeridos'];
        }

        $stmt = $this->pdo->prepare("INSERT INTO products (name, price) VALUES (?, ?)");
        $stmt->execute([$data['name'], $data['price']]);
        return ['message' => 'Producto creado'];
    }

    public function update($id, $data) {
        if (empty($data['name']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            http_response_code(400);
            return ['error' => 'Nombre y precio válidos son requeridos'];
        }

        $stmt = $this->pdo->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['price'], $id]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            return ['error' => 'Producto no encontrado'];
        }

        return ['message' => 'Producto actualizado'];
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            return ['error' => 'Producto no encontrado'];
        }

        return ['message' => 'Producto eliminado'];
    }
}