<?php
// Disable error reporting to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

// Set the content type to JSON
header('Content-Type: application/json');

// Custom error handler to output JSON
function jsonError($message) {
    echo json_encode(['error' => $message]);
    exit;
}

// Set custom error and exception handlers
set_error_handler('jsonError');
set_exception_handler('jsonError');

// Database connection
require_once 'db_connect.php';
$mysqli = db_connect();

// Input sanitization function
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags($input));
}

// Get input
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add_item':
        try {
            // Get and sanitize input
            $name = sanitizeInput($_POST['name']);
            $sku = sanitizeInput($_POST['sku']);
            $quantity = intval($_POST['quantity']);
            $unit = sanitizeInput($_POST['unit']);
            $purchase_price = floatval($_POST['purchase_price']);
            $selling_price = floatval($_POST['selling_price']);
            $supplier_id = intval($_POST['supplier_id']);
            $reorder_level = intval($_POST['reorder_level']);
            $brand_id = intval($_POST['brand_id']);

            // Update how we handle category_ids
            $category_ids = isset($_POST['category_ids']) ? json_decode($_POST['category_ids'], true) : [];
            if (!is_array($category_ids)) {
                $category_ids = [$category_ids]; // Convert single value to array
            }
            // Ensure all category IDs are integers
            $category_ids = array_map('intval', array_filter($category_ids));

            // Verify categories exist before proceeding
            if (!empty($category_ids)) {
                $category_ids_str = implode(',', $category_ids);
                $verify_categories = $mysqli->query("SELECT id FROM categories WHERE id IN ($category_ids_str)");
                
                if (!$verify_categories) {
                    throw new Exception('Failed to verify categories: ' . $mysqli->error);
                }
                
                // Get actual existing category IDs
                $existing_category_ids = [];
                while ($row = $verify_categories->fetch_assoc()) {
                    $existing_category_ids[] = $row['id'];
                }
                
                // Check if all requested categories exist
                $missing_categories = array_diff($category_ids, $existing_category_ids);
                if (!empty($missing_categories)) {
                    throw new Exception('One or more selected categories do not exist: ' . implode(', ', $missing_categories));
                }
            }

            // Start transaction
            $mysqli->begin_transaction();

            // Insert into products table
            $stmt = $mysqli->prepare("
                INSERT INTO products (
                    name, sku, quantity, unit, purchase_price, selling_price, 
                    supplier_id, reorder_level, brand_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $mysqli->error);
            }

            $stmt->bind_param(
                "ssissdiis",
                $name, $sku, $quantity, $unit, $purchase_price,
                $selling_price, $supplier_id, $reorder_level, $brand_id
            );

            if (!$stmt->execute()) {
                throw new Exception('Failed to add product: ' . $stmt->error);
            }

            $product_id = $stmt->insert_id;

            // Insert product categories
            if (!empty($category_ids)) {
                foreach ($category_ids as $category_id) {
                    if ($category_id > 0) { // Only add valid category IDs
                        $stmt = $mysqli->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
                        if (!$stmt) {
                            throw new Exception('Failed to prepare category insert: ' . $mysqli->error);
                        }
                        $stmt->bind_param("ii", $product_id, $category_id);
                        if (!$stmt->execute()) {
                            throw new Exception('Failed to add product category: ' . $stmt->error);
                        }
                        $stmt->close();
                    }
                }
            }

            // Handle image upload if present
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    throw new Exception('Failed to upload image');
                }

                // Insert into product_images
                $stmt = $mysqli->prepare("
                    INSERT INTO product_images (product_id, image_path, is_primary) 
                    VALUES (?, ?, 1)
                ");
                $stmt->bind_param("is", $product_id, $upload_path);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to save image record: ' . $stmt->error);
                }
            }

            // Commit transaction
            $mysqli->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Item added successfully',
                'product_id' => $product_id
            ]);

        } catch (Exception $e) {
            $mysqli->rollback();
            jsonError('Failed to add item: ' . $e->getMessage());
        }
        break;

    case 'update_item':
        try {
            // Get and sanitize input
            $id = intval($_POST['id']);
            $name = sanitizeInput($_POST['name']);
            $sku = sanitizeInput($_POST['sku']);
            $quantity = intval($_POST['quantity']);
            $unit = sanitizeInput($_POST['unit']);
            $purchase_price = floatval($_POST['purchase_price']);
            $selling_price = floatval($_POST['selling_price']);
            $supplier_id = intval($_POST['supplier_id']);
            $reorder_level = intval($_POST['reorder_level']);
            $brand_id = intval($_POST['brand_id']);
            $category_ids = isset($_POST['category_ids']) ? array_map('intval', (array)$_POST['category_ids']) : [];

            // Start transaction
            $mysqli->begin_transaction();

            // Update products table
            $stmt = $mysqli->prepare("
                UPDATE products SET 
                    name = ?, sku = ?, quantity = ?, unit = ?, 
                    purchase_price = ?, selling_price = ?, supplier_id = ?,
                    reorder_level = ?, brand_id = ?, last_updated = CURRENT_TIMESTAMP
                WHERE id = ?
            ");

            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $mysqli->error);
            }

            $stmt->bind_param(
                "ssissdiisi",
                $name, $sku, $quantity, $unit, $purchase_price,
                $selling_price, $supplier_id, $reorder_level, $brand_id, $id
            );

            if (!$stmt->execute()) {
                throw new Exception('Failed to update product: ' . $stmt->error);
            }

            // Update categories
            $mysqli->query("DELETE FROM product_categories WHERE product_id = $id");
            
            if (!empty($category_ids)) {
                $category_values = array_map(function($cat_id) use ($id) {
                    return "($id, $cat_id)";
                }, $category_ids);

                $category_query = "INSERT INTO product_categories (product_id, category_id) VALUES " . 
                                implode(", ", $category_values);

                if (!$mysqli->query($category_query)) {
                    throw new Exception('Failed to update product categories: ' . $mysqli->error);
                }
            }

            // Handle image upload if present
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    throw new Exception('Failed to upload image');
                }

                // Update product_images
                $mysqli->query("DELETE FROM product_images WHERE product_id = $id AND is_primary = 1");
                
                $stmt = $mysqli->prepare("
                    INSERT INTO product_images (product_id, image_path, is_primary) 
                    VALUES (?, ?, 1)
                ");
                $stmt->bind_param("is", $id, $upload_path);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to save image record: ' . $stmt->error);
                }
            }

            // Commit transaction
            $mysqli->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Item updated successfully'
            ]);

        } catch (Exception $e) {
            $mysqli->rollback();
            jsonError('Failed to update item: ' . $e->getMessage());
        }
        break;

    case 'delete_item':
        try {
            $id = intval($_POST['id']);

            // Start transaction
            $mysqli->begin_transaction();

            // Delete related records first
            $mysqli->query("DELETE FROM product_categories WHERE product_id = $id");
            $mysqli->query("DELETE FROM product_images WHERE product_id = $id");
            
            // Delete the product
            $stmt = $mysqli->prepare("DELETE FROM products WHERE id = ?");
            $stmt->bind_param("i", $id);

            if (!$stmt->execute()) {
                throw new Exception('Failed to delete product: ' . $stmt->error);
            }

            if ($stmt->affected_rows === 0) {
                throw new Exception('Product not found');
            }

            // Commit transaction
            $mysqli->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Item deleted successfully'
            ]);

        } catch (Exception $e) {
            $mysqli->rollback();
            jsonError('Failed to delete item: ' . $e->getMessage());
        }
        break;

    case 'get_categories':
        try {
            // Simplified query to get just id and name
            $stmt = $mysqli->prepare("
                SELECT id, name
                FROM categories
                ORDER BY name
            ");
            
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $mysqli->error);
            }
            
            if (!$stmt->execute()) {
                throw new Exception('Execute failed: ' . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                $categories[] = [
                    'id' => (int)$row['id'],
                    'name' => $row['name']
                ];
            }
            
            // Return in the exact format needed
            echo json_encode([
                'categories' => $categories
            ]);

        } catch (Exception $e) {
            jsonError('Failed to fetch categories: ' . $e->getMessage());
        }
        break;

    case 'add_category':
        try {
            $name = sanitizeInput($_POST['name']);
            $description = sanitizeInput($_POST['description'] ?? '');
            
            // First check if category already exists
            $check_stmt = $mysqli->prepare("SELECT id FROM categories WHERE name = ?");
            if (!$check_stmt) {
                throw new Exception('Failed to prepare check statement: ' . $mysqli->error);
            }
            
            $check_stmt->bind_param("s", $name);
            if (!$check_stmt->execute()) {
                throw new Exception('Failed to check category: ' . $check_stmt->error);
            }
            
            $check_result = $check_stmt->get_result();
            if ($check_result->num_rows > 0) {
                throw new Exception('Category already exists');
            }
            $check_stmt->close();
            
            // If category doesn't exist, insert it
            $stmt = $mysqli->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            if (!$stmt) {
                throw new Exception('Failed to prepare insert statement: ' . $mysqli->error);
            }
            
            $stmt->bind_param("ss", $name, $description);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to add category: ' . $stmt->error);
            }
            
            $category_id = $stmt->insert_id;
            $stmt->close();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Category added successfully',
                'category' => [
                    'id' => $category_id,
                    'name' => $name,
                    'description' => $description
                ]
            ]);
            
        } catch (Exception $e) {
            jsonError('Failed to add category: ' . $e->getMessage());
        }
        break;

    case 'get_suppliers':
        try {
            $stmt = $mysqli->prepare("SELECT id, name, contact_name, email, phone, address, category FROM suppliers ORDER BY name");
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $mysqli->error);
            }
            
            if (!$stmt->execute()) {
                throw new Exception('Execute failed: ' . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $suppliers = [];
            while ($row = $result->fetch_assoc()) {
                $suppliers[] = $row;
            }
            
            echo json_encode(['suppliers' => $suppliers]);
        } catch (Exception $e) {
            jsonError('Failed to fetch suppliers: ' . $e->getMessage());
        }
        break;

    case 'get_brands':
        try {
            $stmt = $mysqli->prepare("SELECT id, name FROM brands ORDER BY name");
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $mysqli->error);
            }
            
            if (!$stmt->execute()) {
                throw new Exception('Execute failed: ' . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $brands = [];
            while ($row = $result->fetch_assoc()) {
                $brands[] = $row;
            }
            
            echo json_encode(['brands' => $brands]);
        } catch (Exception $e) {
            jsonError('Failed to fetch brands: ' . $e->getMessage());
        }
        break;

    case 'add_brand':
        try {
            $name = sanitizeInput($_POST['name']);
            
            $stmt = $mysqli->prepare("INSERT INTO brands (name) VALUES (?)");
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $mysqli->error);
            }
            
            $stmt->bind_param("s", $name);
            
            if (!$stmt->execute()) {
                throw new Exception('Execute failed: ' . $stmt->error);
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Brand added successfully',
                'brand' => [
                    'id' => $stmt->insert_id,
                    'name' => $name
                ]
            ]);
        } catch (Exception $e) {
            jsonError('Failed to add brand: ' . $e->getMessage());
        }
        break;

    default:
        jsonError('Invalid action');
}

$mysqli->close();
?>