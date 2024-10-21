<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'obtenerProductos') {
    obtenerProductos();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'crearProducto') {
    crearProducto();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'eliminarProducto') {
    eliminarProducto();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'actualizarProducto') {
    actualizarProducto();
}

function obtenerProductos() {
    global $conn;

    $sql = "SELECT * FROM productos";
    $result = $conn->query($sql);

    $productos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }
    echo json_encode($productos);
}

function crearProducto() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['nombre']) || !isset($data['descripcion']) || !isset($data['precio']) || !isset($data['stock'])) {
        echo json_encode(['error' => 'Datos incompletos']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $data['nombre'], $data['descripcion'], $data['precio'], $data['stock']);

    if ($stmt->execute()) {
        $id_producto = $stmt->insert_id;
        echo json_encode(['success' => 'Producto creado con éxito', 'id_producto' => $id_producto]);
    } else {
        echo json_encode(['error' => 'Error al crear el producto: ' . $conn->error]);
    }
    $stmt->close();
}

function eliminarProducto() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        echo json_encode(['error' => 'ID de producto no especificado']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $data['id']);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Producto eliminado con éxito']);
    } else {
        echo json_encode(['error' => 'Error al eliminar el producto: ' . $conn->error]);
    }
    $stmt->close();
}

function actualizarProducto() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || !isset($data['nombre']) || !isset($data['descripcion']) || !isset($data['precio']) || !isset($data['stock'])) {
        echo json_encode(['error' => 'Datos incompletos']);
        return;
    }

    $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=? WHERE id=?");
    $stmt->bind_param("ssdii", $data['nombre'], $data['descripcion'], $data['precio'], $data['stock'], $data['id']);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Producto actualizado con éxito']);
    } else {
        echo json_encode(['error' => 'Error al actualizar el producto: ' . $conn->error]);
    }
    $stmt->close();
}
?>
