<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'obtenerVentas') {
    obtenerVentas();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'crearVenta') {
    crearVenta();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'eliminarVenta') {
    eliminarVenta();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'actualizarVenta') {
    actualizarVenta();
}

function obtenerVentas() {
    global $conn;

    $sql = "SELECT v.id, v.usuario_id, v.total, v.fecha_venta, u.nombre AS vendedor FROM ventas v INNER JOIN usuarios u ON v.usuario_id = u.id";
    $result = $conn->query($sql);

    $ventas = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $ventas[] = $row;
        }
    }
    echo json_encode($ventas);
}

function crearVenta() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['usuario_id']) || !isset($data['total'])) {
        echo json_encode(['error' => 'Datos incompletos']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO ventas (usuario_id, total) VALUES (?, ?)");
    $stmt->bind_param("id", $data['usuario_id'], $data['total']);

    if ($stmt->execute()) {
        // Obtener el ID de la nueva venta
        $id_venta = $stmt->insert_id;
        echo json_encode(['success' => 'Venta creada con éxito', 'id_venta' => $id_venta]);
    } else {
        echo json_encode(['error' => 'Error al crear la venta: ' . $conn->error]);
    }
    $stmt->close();
}

function eliminarVenta() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        echo json_encode(['error' => 'ID de venta no especificado']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM ventas WHERE id = ?");
    $stmt->bind_param("i", $data['id']);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Venta eliminada con éxito']);
    } else {
        echo json_encode(['error' => 'Error al eliminar la venta: ' . $conn->error]);
    }
    $stmt->close();
}

function actualizarVenta() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || !isset($data['usuario_id']) || !isset($data['total'])) {
        echo json_encode(['error' => 'Datos incompletos']);
        return;
    }

    $stmt = $conn->prepare("UPDATE ventas SET usuario_id=?, total=? WHERE id=?");
    $stmt->bind_param("idi", $data['usuario_id'], $data['total'], $data['id']);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Venta actualizada con éxito']);
    } else {
        echo json_encode(['error' => 'Error al actualizar la venta: ' . $conn->error]);
    }
    $stmt->close();
}
?>
