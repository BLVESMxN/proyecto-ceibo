<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'obtenerPagos') {
    obtenerPagos();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'crearPago') {
    crearPago();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'eliminarPago') {
    eliminarPago();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'actualizarPago') {
    actualizarPago();
}

function obtenerPagos() {
    global $conn;

    $sql = "SELECT p.id, p.venta_id, p.metodo_pago, p.monto, p.fecha_pago 
            FROM pagos p 
            INNER JOIN ventas v ON p.venta_id = v.id";
    $result = $conn->query($sql);

    $pagos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pagos[] = $row;
        }
    }
    echo json_encode($pagos);
}

function crearPago() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['venta_id']) || !isset($data['metodo_pago']) || !isset($data['monto'])) {
        echo json_encode(['error' => 'Datos incompletos']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO pagos (venta_id, metodo_pago, monto) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $data['venta_id'], $data['metodo_pago'], $data['monto']);

    if ($stmt->execute()) {
        $id_pago = $stmt->insert_id;
        echo json_encode(['success' => 'Pago creado con éxito', 'id_pago' => $id_pago]);
    } else {
        echo json_encode(['error' => 'Error al crear el pago: ' . $conn->error]);
    }
    $stmt->close();
}

function eliminarPago() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        echo json_encode(['error' => 'ID de pago no especificado']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM pagos WHERE id = ?");
    $stmt->bind_param("i", $data['id']);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Pago eliminado con éxito']);
    } else {
        echo json_encode(['error' => 'Error al eliminar el pago: ' . $conn->error]);
    }
    $stmt->close();
}

function actualizarPago() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || !isset($data['venta_id']) || !isset($data['metodo_pago']) || !isset($data['monto'])) {
        echo json_encode(['error' => 'Datos incompletos']);
        return;
    }

    $stmt = $conn->prepare("UPDATE pagos SET venta_id=?, metodo_pago=?, monto=? WHERE id=?");
    $stmt->bind_param("isdi", $data['venta_id'], $data['metodo_pago'], $data['monto'], $data['id']);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Pago actualizado con éxito']);
    } else {
        echo json_encode(['error' => 'Error al actualizar el pago: ' . $conn->error]);
    }
    $stmt->close();
}
?>
