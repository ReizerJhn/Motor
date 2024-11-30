<?php
require_once 'db_connect.php';
$mysqli = db_connect();

header('Content-Type: application/json');

try {
    // Fetch inventory data with related information
    $query = "
        SELECT 
            p.*,
            b.name AS brand_name,
            s.name AS supplier_name,
            GROUP_CONCAT(DISTINCT c.name) AS categories,
            pi.image_path
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.id
        LEFT JOIN suppliers s ON p.supplier_id = s.id
        LEFT JOIN product_categories pc ON p.id = pc.product_id
        LEFT JOIN categories c ON pc.category_id = c.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        GROUP BY p.id
        ORDER BY p.date_added DESC
    ";

    $result = $mysqli->query($query);
    if (!$result) {
        throw new Exception("Query failed: " . $mysqli->error);
    }

    $inventoryData = [];
    $lowStockCount = 0;
    $outOfStockCount = 0;
    $totalValue = 0;

    while ($row = $result->fetch_assoc()) {
        // Calculate inventory status
        if ($row['quantity'] == 0) {
            $outOfStockCount++;
        } elseif ($row['quantity'] <= $row['reorder_level']) {
            $lowStockCount++;
        }
        
        // Calculate total inventory value
        $totalValue += $row['quantity'] * $row['purchase_price'];
        
        // Format categories as array
        $row['categories'] = $row['categories'] ? explode(',', $row['categories']) : [];
        
        $inventoryData[] = $row;
    }

    echo json_encode([
        'products' => $inventoryData,
        'inventoryStatus' => [
            'totalItems' => count($inventoryData),
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'totalValue' => $totalValue
        ]
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$mysqli->close();
?>