<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set("America/Mexico_City");

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(["ok" => false, "mensaje" => "Debes iniciar sesión"]);
    exit;
}

include("conexion.php");
$conn->set_charset("utf8mb4");
$id = (int)$_SESSION['id_usuario'];

// Datos del usuario + progreso (nunca se selecciona la contraseña)
$stmt = $conn->prepare("
    SELECT u.id_usuario, u.nombre, u.email, u.pais, u.nivel, u.fecha_registro,
           COALESCE(p.experiencia, 0) AS xp,
           COALESCE(p.porcentaje, 0)  AS porcentaje
    FROM usuario u
    LEFT JOIN progreso p ON p.id_usuario = u.id_usuario AND p.id_curso = 1
    WHERE u.id_usuario = ?
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$u) {
    http_response_code(404);
    echo json_encode(["ok" => false, "mensaje" => "Usuario no encontrado"]);
    exit;
}

// Actividad por día
$act = [];
$stmt = $conn->prepare("SELECT fecha, xp FROM actividad WHERE id_usuario = ?");
if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $act[$r['fecha']] = (int)$r['xp'];
    }
    $stmt->close();
}

// Racha en días: seguidos hasta hoy (o hasta ayer si hoy aún no practica)
$d = new DateTime("today");
if (!isset($act[$d->format("Y-m-d")])) {
    $d->modify("-1 day");
}
$racha = 0;
while (isset($act[$d->format("Y-m-d")])) {
    $racha++;
    $d->modify("-1 day");
}

// Últimos 7 días
$semana = [];
for ($k = 6; $k >= 0; $k--) {
    $f = date("Y-m-d", strtotime("-$k day"));
    $semana[] = ["fecha" => $f, "xp" => $act[$f] ?? 0];
}

echo json_encode([
    "ok"               => true,
    "id"               => (int)$u['id_usuario'],
    "nombre"           => $u['nombre'],
    "email"            => $u['email'],
    "pais"             => $u['pais'],
    "nivel_registro"   => (int)$u['nivel'],
    "fecha_registro"   => $u['fecha_registro'],
    "xp"               => (int)$u['xp'],
    "porcentaje"       => (int)$u['porcentaje'],
    "racha_dias"       => $racha,
    "dias_practicados" => count($act),
    "hoy_xp"           => $act[date("Y-m-d")] ?? 0,
    "semana"           => $semana
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>
