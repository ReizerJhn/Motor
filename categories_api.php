<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = $_POST;
        
        // Add new category
        if ($data['action'] === 'add_category') {
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->execute([$data['name'], $data['description']]);
            
            echo json_encode(['success' => true, 'message' => 'Category added successfully']);
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Fetch categories
        if ($_GET['action'] === 'get_categories') {
            $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($categories);
            exit;
        }
    }

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 