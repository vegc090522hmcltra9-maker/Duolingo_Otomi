<?php
session_start();
header("Content-Type: application/json");
date_default_timezone_set("America/Mexico_City");

$conexion = new mysqli("localhost", "root", "", "d_o_axel_502");
if ($conexion->connect_error) {
    echo json_encode(["success" => false, "mensaje" => "Error de conexión"]);
    exit;
}

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["success" => false, "mensaje" => "Debes iniciar sesión"]);
    exit;
}

$datos = json_decode(file_get_contents("php://input"), true);
$puntajeGanado = (int)($datos["experienciaGanada"] ?? 0);
$idUsuario = (int)$_SESSION["id_usuario"];
$hoy = date("Y-m-d");

if ($puntajeGanado <= 0) {
    echo json_encode(["success" => true, "experienciaGanada" => 0]);
    exit;
}

// Si el usuario aún no tiene fila de progreso, se crea (si no, el UPDATE no afectaría nada)
$ins = $conexion->prepare("
    INSERT INTO progreso (porcentaje, experiencia, racha, fecha_registro, id_usuario, id_curso)
    SELECT 0, 0, 0, ?, ?, 1
    WHERE NOT EXISTS (SELECT 1 FROM progreso WHERE id_usuario = ?)
");
$ins->bind_param("sii", $hoy, $idUsuario, $idUsuario);
$ins->execute();
$ins->close();

// Progreso acumulado (tope de 100 de experiencia, como antes)
$stmt = $conexion->prepare("
    UPDATE progreso
    SET experiencia = LEAST(experiencia + ?, 100),
        racha       = racha + 1,
        porcentaje  = LEAST(porcentaje + 5, 100)
    WHERE id_usuario = ?
");
$stmt->bind_param("ii", $puntajeGanado, $idUsuario);
$resultado = $stmt->execute();
$stmt->close();

// XP del día (alimenta racha en días, meta diaria y gráfica del perfil)
$act = $conexion->prepare("
    INSERT INTO actividad (id_usuario, fecha, xp) VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE xp = xp + VALUES(xp)
");
$act->bind_param("isi", $idUsuario, $hoy, $puntajeGanado);
$act->execute();
$act->close();

echo json_encode(["success" => $resultado, "experienciaGanada" => $puntajeGanado]);
$conexion->close();
?>
