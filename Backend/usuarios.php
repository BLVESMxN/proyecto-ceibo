<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'obtenerUsuarios') {
    obtenerUsuarios();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'crearUsuario') {
    crearUsuario();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'eliminarUsuario') {
    eliminarUsuario();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'actualizarUsuario') {
    actualizarUsuario();
}

function obtenerUsuarios() {
    global $conn;

    $sql = "SELECT id, nombre, email, rol, fecha_creacion FROM usuarios";
    $result = $conn->query($sql);

    $usuarios = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }
    echo json_encode($usuarios);
}

function crearUsuario() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['nombre']) || !isset($data['email']) || !isset($data['password']) || !isset($data['rol'])) {
        echo json_encode(['error' => 'Datos incompletos']);
        return;
    }

    // Hash de la contraseña
    $password_hashed = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $data['nombre'], $data['email'], $password_hashed, $data['rol']);

    if ($stmt->execute()) {
        // Obtener el ID del nuevo usuario
        $id_usuario = $stmt->insert_id;
        echo json_encode(['success' => 'Usuario creado con éxito', 'id_usuario' => $id_usuario]);
    } else {
        echo json_encode(['error' => 'Error al crear el usuario: ' . $conn->error]);
    }
    $stmt->close();
}

function eliminarUsuario() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        echo json_encode(['error' => 'ID de usuario no especificado']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $data['id']);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Usuario eliminado con éxito']);
    } else {
        echo json_encode(['error' => 'Error al eliminar el usuario: ' . $conn->error]);
    }
    $stmt->close();
}

function actualizarUsuario() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || !isset($data['nombre']) || !isset($data['email']) || !isset($data['password']) || !isset($data['rol'])) {
        echo json_encode(['error' => 'Datos incompletos']);
        return;
    }

    // Hash de la nueva contraseña
    $password_hashed = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, email=?, password=?, rol=? WHERE id=?");
    $stmt->bind_param("ssssi", $data['nombre'], $data['email'], $password_hashed, $data['rol'], $data['id']);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Usuario actualizado con éxito']);
    } else {
        echo json_encode(['error' => 'Error al actualizar el usuario: ' . $conn->error]);
    }
    $stmt->close();
}
?>
