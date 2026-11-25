<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(["ok" => false, "mensaje" => "Debes iniciar sesión"]);
    exit;
}

$datos  = json_decode(file_get_contents("php://input"), true);
$nombre = trim($datos["nombre"] ?? "");

if ($nombre === "" || mb_strlen($nombre) > 50) {
    echo json_encode(["ok" => false, "mensaje" => "El nombre debe tener entre 1 y 50 caracteres"]);
    exit;
}

include("conexion.php");
$conn->set_charset("utf8mb4");
$id = (int)$_SESSION['id_usuario'];

$stmt = $conn->prepare("UPDATE usuario SET nombre = ? WHERE id_usuario = ?");
$stmt->bind_param("si", $nombre, $id);
$ok = $stmt->execute();

if ($ok) {
    $_SESSION['nombre'] = $nombre;
}
echo json_encode(["ok" => $ok, "mensaje" => $ok ? "Guardado" : "No se pudo guardar"]);

$stmt->close();
$conn->close();
?>
