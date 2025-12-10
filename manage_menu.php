<?php
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'restaurant_db_temp';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $name = $conn->real_escape_string($_POST['name'] ?? '');
        $description = $conn->real_escape_string($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $image_url = $conn->real_escape_string($_POST['image_url'] ?? '');
        
        if (empty($name) || empty($price)) {
            echo json_encode(['error' => 'Name and price are required']);
            exit;
        }
        
        $sql = "INSERT INTO menu_items (name, description, price, image_url) VALUES ('$name', '$description', $price, '$image_url')";
        
        if ($conn->query($sql)) {
            $id = $conn->insert_id;
            echo json_encode(['success' => true, 'id' => $id, 'message' => 'Item added successfully']);
        } else {
            echo json_encode(['error' => 'Error adding item: ' . $conn->error]);
        }
        break;
        
    case 'edit':
        $id = intval($_POST['id'] ?? 0);
        $name = $conn->real_escape_string($_POST['name'] ?? '');
        $description = $conn->real_escape_string($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $image_url = $conn->real_escape_string($_POST['image_url'] ?? '');
        
        if (empty($id) || empty($name) || empty($price)) {
            echo json_encode(['error' => 'ID, name and price are required']);
            exit;
        }
        
        $sql = "UPDATE menu_items SET name='$name', description='$description', price=$price, image_url='$image_url' WHERE id=$id";
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
        } else {
            echo json_encode(['error' => 'Error updating item: ' . $conn->error]);
        }
        break;
        
    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        
        if (empty($id)) {
            echo json_encode(['error' => 'ID is required']);
            exit;
        }
        
        $sql = "DELETE FROM menu_items WHERE id=$id";
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
        } else {
            echo json_encode(['error' => 'Error deleting item: ' . $conn->error]);
        }
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}

$conn->close();
?>
