<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
require_once 'db_connect.php';

try {
    $mysqli = db_connect();
    
    $query = "SELECT id, name FROM products ORDER BY name ASC";
    $result = $mysqli->query($query);
    
    if (!$result) {
        throw new Exception($mysqli->error);
    }
    
    $products = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['success' => true, 'products' => $products]);

} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
