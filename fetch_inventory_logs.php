<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
require_once 'db_connect.php';

try {
    $mysqli = db_connect();
    
    $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : '';
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    $created_by = isset($_GET['created_by']) ? $_GET['created_by'] : '';
    
    $query = "
        SELECT 
            it.id,
            p.name as product_name,
            it.quantity_change,
            it.transaction_type,
            it.created_at,
            u.name as created_by,
            u.id as user_id,
            it.notes,
            it.status
        FROM inventory_transactions it
        JOIN products p ON it.product_id = p.id
        LEFT JOIN users u ON it.created_by = u.id
        WHERE 1=1
    ";
    
    if ($product_id) {
        $product_id = $mysqli->real_escape_string($product_id);
        $query .= " AND it.product_id = '$product_id'";
    }
    
    if ($start_date) {
        $start_date = $mysqli->real_escape_string($start_date);
        $query .= " AND DATE(it.created_at) >= '$start_date'";
    }
    
    if ($end_date) {
        $end_date = $mysqli->real_escape_string($end_date);
        $query .= " AND DATE(it.created_at) <= '$end_date'";
    }
    
    if ($created_by) {
        $created_by = $mysqli->real_escape_string($created_by);
        $query .= " AND it.created_by = '$created_by'";
    }
    
    $query .= " ORDER BY it.created_at DESC LIMIT 100";
    
    $result = $mysqli->query($query);
    
    if (!$result) {
        throw new Exception($mysqli->error);
    }
    
    $logs = $result->fetch_all(MYSQLI_ASSOC);
    
    // Fetch users for the filter dropdown
    $usersQuery = "SELECT id, name FROM users ORDER BY name ASC";
    $usersResult = $mysqli->query($usersQuery);
    $users = $usersResult->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true, 
        'logs' => $logs,
        'users' => $users
    ]);

} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
