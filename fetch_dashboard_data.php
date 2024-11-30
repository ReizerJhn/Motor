<?php
// Disable error reporting for production
error_reporting(0);
ini_set('display_errors', 0);

// Set the content type to JSON
header('Content-Type: application/json');

// Function to handle errors and output JSON
function outputError($message) {
    echo json_encode(['error' => $message]);
    exit;
}

// Database connection
require_once 'db_connect.php';
$mysqli = db_connect();

// Function to execute query and handle errors
function executeQuery($mysqli, $query) {
    $result = $mysqli->query($query);
    if (!$result) {
        outputError('Query failed: ' . $mysqli->error . ' SQL: ' . $query);
    }
    return $result;
}

try {
    // Get inventory summary from the view
    $inventoryQuery = "SELECT * FROM vw_inventory_summary";
    $inventoryResult = executeQuery($mysqli, $inventoryQuery);
    
    $inventoryData = [];
    $lowStockCount = 0;
    $outOfStockCount = 0;
    
    while ($row = $inventoryResult->fetch_assoc()) {
        $inventoryData[] = [
            'name' => $row['name'],
            'in_stock' => (int)$row['in_stock'],
            'out_of_stock' => (int)$row['out_of_stock']
        ];
        
        if ($row['out_of_stock'] == 1) {
            $outOfStockCount++;
        } elseif ($row['in_stock'] <= $row['low_stock_threshold']) {
            $lowStockCount++;
        }
    }

    // Calculate total inventory value
    $totalValueQuery = "SELECT SUM(quantity * purchase_price) as total_value FROM products";
    $totalValueResult = executeQuery($mysqli, $totalValueQuery);
    $totalValueRow = $totalValueResult->fetch_assoc();
    $totalValue = $totalValueRow['total_value'] ?? 0;

    // Get parts by type from the view
    $partTypeQuery = "SELECT * FROM vw_parts_by_type";
    $partTypeResult = executeQuery($mysqli, $partTypeQuery);
    
    $partTypeData = [];
    while ($row = $partTypeResult->fetch_assoc()) {
        $partTypeData[] = [
            'type' => $row['type'],
            'total_quantity' => (int)$row['total_quantity']
        ];
    }

    // Get weekly order volume from the view
    $weeklyOrderQuery = "SELECT * FROM vw_weekly_order_volume";
    $weeklyOrderResult = executeQuery($mysqli, $weeklyOrderQuery);
    
    $weeklyOrderVolume = [];
    while ($row = $weeklyOrderResult->fetch_assoc()) {
        $weeklyOrderVolume[] = [
            'order_date' => $row['order_date'],
            'order_count' => (int)$row['order_count'],
            'total_amount' => (float)$row['total_amount']
        ];
    }

    // Get recent orders with customer names
    $recentOrdersQuery = "
        SELECT 
            o.id,
            COALESCE(c.name, 'Walk-in Customer') as customer,
            o.total_amount as total,
            o.order_date
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        ORDER BY o.order_date DESC
        LIMIT 5
    ";
    $recentOrdersResult = executeQuery($mysqli, $recentOrdersQuery);
    
    $recentOrders = [];
    while ($row = $recentOrdersResult->fetch_assoc()) {
        $recentOrders[] = [
            'id' => $row['id'],
            'customer' => $row['customer'],
            'total' => (float)$row['total'],
            'order_date' => $row['order_date']
        ];
    }

    // Prepare the response
    $response = [
        'inventoryData' => $inventoryData,
        'partTypeData' => $partTypeData,
        'weeklyOrderVolume' => $weeklyOrderVolume,
        'recentOrders' => $recentOrders,
        'inventoryStatus' => [
            'totalItems' => count($inventoryData),
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'totalValue' => floatval($totalValue)
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    outputError('An unexpected error occurred: ' . $e->getMessage());
} finally {
    if ($mysqli) {
        $mysqli->close();
    }
}
?>
