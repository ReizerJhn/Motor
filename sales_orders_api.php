<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'db_connect.php';
session_start();

header('Content-Type: application/json');

try {
    $database = new Database();
    $pdo = $database->connect();

    $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 44;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'get_orders':
                    $stmt = $pdo->query("
                        SELECT o.*, c.name as customer_name 
                        FROM orders o 
                        JOIN customers c ON o.customer_id = c.id 
                        ORDER BY o.order_date DESC
                    ");
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($orders as &$order) {
                        $stmt = $pdo->prepare("
                            SELECT p.name 
                            FROM order_items oi 
                            JOIN products p ON oi.product_id = p.id 
                            WHERE oi.order_id = ?
                        ");
                        $stmt->execute([$order['id']]);
                        $products = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        $order['products'] = $products;
                    }
                    
                    echo json_encode(['success' => true, 'orders' => $orders]);
                    break;

                case 'get_products':
                    $stmt = $pdo->query("SELECT id, name, quantity, selling_price FROM products");
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['success' => true, 'products' => $products]);
                    break;

                case 'get_order_details':
                    if (!isset($_GET['order_id'])) {
                        throw new Exception('Order ID is required');
                    }
                    
                    $stmt = $pdo->prepare("
                        SELECT o.*, c.name as customer_name,
                        (SELECT GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ')'))
                         FROM order_items oi 
                         JOIN products p ON oi.product_id = p.id 
                         WHERE oi.order_id = o.id) as products_list
                        FROM orders o
                        JOIN customers c ON o.customer_id = c.id
                        WHERE o.id = ?
                    ");
                    $stmt->execute([$_GET['order_id']]);
                    $order = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$order) {
                        throw new Exception('Order not found');
                    }
                    
                    // Get order items
                    $stmt = $pdo->prepare("
                        SELECT oi.*, p.name as product_name
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.id
                        WHERE oi.order_id = ?
                    ");
                    $stmt->execute([$_GET['order_id']]);
                    $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo json_encode(['success' => true, 'order' => $order]);
                    break;

                case 'get_transactions':
                    $stmt = $pdo->query("
                        SELECT 
                            t.*,
                            p.name as product_name,
                            u.username as created_by_name
                        FROM inventory_transactions t
                        JOIN products p ON t.product_id = p.id
                        JOIN users u ON t.created_by = u.id
                        ORDER BY t.created_at DESC
                        LIMIT 100
                    ");
                    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['success' => true, 'transactions' => $transactions]);
                    break;

                case 'get_purchase_transactions':
                    $where_clauses = ["t.transaction_type = 'Purchase'", "t.quantity_change > 0"];
                    $params = [];

                    if (isset($_GET['date']) && $_GET['date']) {
                        $where_clauses[] = "DATE(t.created_at) = ?";
                        $params[] = $_GET['date'];
                    }

                    if (isset($_GET['product_id']) && $_GET['product_id']) {
                        $where_clauses[] = "t.product_id = ?";
                        $params[] = $_GET['product_id'];
                    }

                    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);

                    $stmt = $pdo->prepare("
                        SELECT 
                            t.*,
                            p.name as product_name,
                            u.username as created_by_name,
                            u.name as user_full_name
                        FROM inventory_transactions t
                        JOIN products p ON t.product_id = p.id
                        JOIN users u ON t.created_by = u.id
                        $where_sql
                        ORDER BY t.created_at DESC
                        LIMIT 100
                    ");
                    
                    $stmt->execute($params);
                    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['success' => true, 'transactions' => $transactions]);
                    break;

                default:
                    throw new Exception('Invalid action specified');
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['action'])) {
            throw new Exception('No action specified');
        }

        $data = $_POST;
        
        switch ($data['action']) {
            case 'stock_in':
                // Validate incoming data
                if (!isset($data['items']) || !isset($data['quantities'])) {
                    throw new Exception('Missing required fields for stock in');
                }

                $items = json_decode($data['items']);
                $quantities = json_decode($data['quantities']);

                if (empty($items) || empty($quantities)) {
                    throw new Exception('No items provided for stock in');
                }

                $pdo->beginTransaction();
                
                try {
                    // Verify user
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
                    $stmt->execute([$currentUserId]);
                    if (!$stmt->fetch()) {
                        throw new Exception('Invalid user ID');
                    }

                    // Prepare statements
                    $updateStmt = $pdo->prepare("
                        UPDATE products 
                        SET quantity = quantity + ? 
                        WHERE id = ?
                    ");

                    $txnStmt = $pdo->prepare("
                        INSERT INTO inventory_transactions 
                        (product_id, quantity_change, transaction_type, status, created_by, notes) 
                        VALUES (?, ?, 'Purchase', 'Completed', ?, ?)
                    ");

                    // Process each item
                    for ($i = 0; $i < count($items); $i++) {
                        $itemId = $items[$i];
                        $quantity = abs($quantities[$i]);

                        // Update product quantity
                        $updateStmt->execute([$quantity, $itemId]);
                        
                        if ($updateStmt->rowCount() === 0) {
                            throw new Exception('Product not found: ' . $itemId);
                        }
                        
                        // Record inventory transaction with positive quantity for purchase
                        $txnStmt->execute([
                            $itemId, 
                            $quantity,
                            $currentUserId,
                            "Stock Purchase"
                        ]);
                    }
                    
                    $pdo->commit();
                    echo json_encode(['success' => true]);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    throw $e;
                }
                break;

            case 'create_order':
                if (!isset($data['customer_name'])) {
                    throw new Exception('Customer name is required');
                }

                $pdo->beginTransaction();
                
                try {
                    // Create customer if doesn't exist
                    $stmt = $pdo->prepare("
                        INSERT INTO customers (name) 
                        VALUES (?) 
                        ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)
                    ");
                    $stmt->execute([$data['customer_name']]);
                    $customer_id = $pdo->lastInsertId();

                    // Generate order number
                    $order_number = 'ORD-' . date('Ymd') . '-' . sprintf('%04d', rand(0, 9999));

                    // Add stock validation
                    $products = json_decode($data['products']);
                    $quantities = json_decode($data['quantities']);
                    $prices = json_decode($data['prices']);

                    // Check stock availability for all products
                    $stmt = $pdo->prepare("SELECT id, name, quantity FROM products WHERE id = ?");
                    
                    for ($i = 0; $i < count($products); $i++) {
                        $stmt->execute([$products[$i]]);
                        $product = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if (!$product) {
                            throw new Exception("Product not found");
                        }
                        
                        if ($product['quantity'] < $quantities[$i]) {
                            throw new Exception("Insufficient stock for product: " . $product['name'] . 
                                              ". Available: " . $product['quantity'] . 
                                              ", Requested: " . $quantities[$i]);
                        }
                    }

                    // If we get here, all stock checks passed
                    // Continue with order creation...
                    
                    // Create order
                    $stmt = $pdo->prepare("
                        INSERT INTO orders (
                            order_number, customer_id, order_date, total_amount, 
                            status, payment_status, labor_hours, labor_rate, 
                            tax_rate, discount
                        ) VALUES (
                            ?, ?, ?, ?, 
                            'Pending', 'Pending', ?, ?,
                            ?, ?
                        )
                    ");
                    $stmt->execute([
                        $order_number,
                        $customer_id,
                        $data['order_date'],
                        $data['total_amount'],
                        $data['labor_hours'],
                        $data['labor_rate'],
                        $data['tax_rate'],
                        $data['discount']
                    ]);
                    
                    $order_id = $pdo->lastInsertId();

                    // Create order items and record inventory transactions
                    $stmt = $pdo->prepare("
                        INSERT INTO order_items (
                            order_id, product_id, quantity, price
                        ) VALUES (?, ?, ?, ?)
                    ");

                    // Prepare inventory transaction statement
                    $invTxnStmt = $pdo->prepare("
                        INSERT INTO inventory_transactions (
                            product_id, 
                            quantity_change, 
                            transaction_type, 
                            reference_id,
                            status, 
                            created_by,
                            notes
                        ) VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");

                    for ($i = 0; $i < count($products); $i++) {
                        // Create order item
                        $stmt->execute([
                            $order_id,
                            $products[$i],
                            $quantities[$i],
                            $prices[$i]
                        ]);

                        // Update product quantity
                        $pdo->prepare("
                            UPDATE products 
                            SET quantity = quantity - ? 
                            WHERE id = ?
                        ")->execute([$quantities[$i], $products[$i]]);

                        // Record inventory transaction with negative quantity for sales
                        $invTxnStmt->execute([
                            $products[$i],
                            -abs($quantities[$i]),
                            'Sale',
                            $order_id,
                            'Completed',
                            $currentUserId,
                            "Order #" . $order_number
                        ]);
                    }

                    $pdo->commit();
                    echo json_encode(['success' => true, 'order_id' => $order_id]);
                    
                } catch (Exception $e) {
                    $pdo->rollBack();
                    throw $e;
                }
                break;

            default:
                throw new Exception('Invalid action specified');
        }
    }
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
