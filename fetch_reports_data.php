<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the content type to JSON
header('Content-Type: application/json');

require_once 'db_connect.php';

try {
    $mysqli = db_connect();
    $data = [];

    // Fetch inventory status data using inventory transactions
    $inventoryQuery = "
        SELECT 
            c.name as category,
            SUM(CASE WHEN p.quantity > 0 THEN 1 ELSE 0 END) as in_stock,
            SUM(CASE WHEN p.quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock,
            COUNT(DISTINCT it.id) as transaction_count,
            SUM(CASE 
                WHEN it.transaction_type = 'Purchase' THEN it.quantity_change 
                ELSE 0 
            END) as total_purchases,
            SUM(CASE 
                WHEN it.transaction_type = 'Sale' THEN ABS(it.quantity_change) 
                ELSE 0 
            END) as total_sales
        FROM categories c
        LEFT JOIN product_categories pc ON c.id = pc.category_id
        LEFT JOIN products p ON pc.product_id = p.id
        LEFT JOIN inventory_transactions it ON p.id = it.product_id
            AND it.created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY c.name
        ORDER BY c.name ASC
    ";
    
    $result = $mysqli->query($inventoryQuery);
    $data['inventoryData'] = $result->fetch_all(MYSQLI_ASSOC);
    
    // Fetch sales performance data with transaction details
    $salesQuery = "
        SELECT 
            DATE_FORMAT(it.created_at, '%Y-%m') as month,
            COUNT(DISTINCT o.id) as order_count,
            SUM(o.total_amount) as gross_revenue,
            SUM(o.discount) as total_discounts,
            SUM(o.labor_hours * o.labor_rate) as labor_revenue,
            SUM(o.total_amount * (o.tax_rate / 100)) as tax_amount,
            SUM(o.total_amount - COALESCE(o.discount, 0) + (o.labor_hours * o.labor_rate)) as net_revenue,
            COUNT(DISTINCT o.customer_id) as unique_customers,
            SUM(ABS(it.quantity_change)) as units_sold
        FROM inventory_transactions it
        LEFT JOIN orders o ON it.reference_id = o.id
        WHERE it.transaction_type = 'Sale'
            AND it.status = 'Completed'
            AND it.created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(it.created_at, '%Y-%m')
        ORDER BY month DESC
    ";
    
    $result = $mysqli->query($salesQuery);
    $data['salesData'] = $result->fetch_all(MYSQLI_ASSOC);

    // Fetch top selling products with inventory transaction history
    $topSellingQuery = "
        SELECT 
            p.id,
            p.name,
            p.sku,
            c.name as category,
            p.quantity as current_stock,
            COUNT(DISTINCT o.id) as order_count,
            ABS(SUM(CASE 
                WHEN it.transaction_type = 'Sale' THEN it.quantity_change 
                ELSE 0 
            END)) as total_sold,
            SUM(oi.quantity * oi.price) as total_revenue,
            ROUND(AVG(oi.price), 2) as average_price,
            MAX(it.created_at) as last_sold_date,
            SUM(CASE 
                WHEN it.transaction_type = 'Purchase' THEN it.quantity_change 
                ELSE 0 
            END) as total_restocks,
            COUNT(DISTINCT CASE 
                WHEN it.transaction_type = 'Purchase' THEN it.id 
                ELSE NULL 
            END) as restock_count
        FROM products p
        LEFT JOIN product_categories pc ON p.id = pc.product_id
        LEFT JOIN categories c ON pc.category_id = c.id
        LEFT JOIN inventory_transactions it ON p.id = it.product_id
        LEFT JOIN order_items oi ON p.id = oi.product_id
        LEFT JOIN orders o ON oi.order_id = o.id
        WHERE it.created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
            AND it.status = 'Completed'
        GROUP BY p.id, p.name, p.sku, c.name, p.quantity
        HAVING total_sold > 0
        ORDER BY total_sold DESC
        LIMIT 10
    ";
    
    $result = $mysqli->query($topSellingQuery);
    $data['topSellingProducts'] = $result->fetch_all(MYSQLI_ASSOC);

    // Add daily sales trend with transaction details
    $salesTrendQuery = "
        SELECT 
            DATE_FORMAT(it.created_at, '%Y-%m-%d') as sale_date,
            COUNT(DISTINCT o.id) as orders,
            ABS(SUM(it.quantity_change)) as units_sold,
            SUM(o.total_amount) as gross_revenue,
            SUM(o.discount) as discounts,
            SUM(o.labor_hours * o.labor_rate) as labor_charges,
            SUM(o.total_amount * (o.tax_rate / 100)) as tax_amount,
            SUM(o.total_amount - COALESCE(o.discount, 0) + (o.labor_hours * o.labor_rate)) as net_revenue
        FROM inventory_transactions it
        LEFT JOIN orders o ON it.reference_id = o.id
        WHERE it.transaction_type = 'Sale'
            AND it.status = 'Completed'
            AND it.created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY DATE_FORMAT(it.created_at, '%Y-%m-%d')
        ORDER BY sale_date DESC
    ";
    
    $result = $mysqli->query($salesTrendQuery);
    $data['salesTrend'] = $result->fetch_all(MYSQLI_ASSOC);

    // Fetch low stock items with transaction history
    $lowStockQuery = "
        SELECT 
            p.name,
            p.quantity as current_stock,
            p.reorder_level as reorder_point,
            COALESCE(c.name, 'Uncategorized') as category,
            SUM(CASE 
                WHEN it.transaction_type = 'Sale' 
                AND it.created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                THEN ABS(it.quantity_change) 
                ELSE 0 
            END) as monthly_sales
        FROM products p
        LEFT JOIN product_categories pc ON p.id = pc.product_id
        LEFT JOIN categories c ON pc.category_id = c.id
        LEFT JOIN inventory_transactions it ON p.id = it.product_id
        WHERE p.quantity <= p.reorder_level
            AND p.reorder_level > 0
        GROUP BY p.id, p.name, p.quantity, p.reorder_level, c.name
        ORDER BY (p.reorder_level - p.quantity) DESC, monthly_sales DESC
        LIMIT 10
    ";
    
    $result = $mysqli->query($lowStockQuery);
    $data['lowStockItems'] = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['success' => true, 'data' => $data]);

} catch(Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
