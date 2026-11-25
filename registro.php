<?php
header("Content-Type: application/json");
include("conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'];
$email = $data['email'];
$contrasena = $data['contrasena'];
$fecha_nac = $data['fecha_nac'];
$pais = $data['pais'];
$nivel = $data['nivel'];
$fecha_registro = date("Y-m-d");

// 1. ACUMULADOR DESDE 99
// Si tu MAX es 15, te da 99. Si ya tienes 99, te da 100, luego 101...
$res = $conn->query("SELECT COALESCE(MAX(id_usuario), 0) AS maximo FROM usuario");
$fila = $res->fetch_assoc();

if($fila['maximo'] < 99){
    $nuevo_id = 99;
} else {
    $nuevo_id = $fila['maximo'] + 1;
}

// 2. INSERT con el nuevo ID
$sql = "INSERT INTO usuario (id_usuario, nombre, email, contraseña, fecha_registro, fecha_date, pais, nivel) VALUES (?,?,?,?,?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssssi", $nuevo_id, $nombre, $email, $contrasena, $fecha_registro, $fecha_nac, $pais, $nivel);

if($stmt->execute()){
    // Fila de progreso inicial (sin esto el perfil y actualizar_progreso no tendrían datos)
    $p = $conn->prepare("INSERT INTO progreso (porcentaje, experiencia, racha, fecha_registro, id_usuario, id_curso) VALUES (0, 0, 0, ?, ?, 1)");
    $p->bind_param("si", $fecha_registro, $nuevo_id);
    $p->execute();
    $p->close();
    echo json_encode(["estado" => true, "mensaje" => "✅ Usuario registrado correctamente - ID: " . $nuevo_id]);
} else {
    echo json_encode(["estado" => false, "mensaje" => "❌ Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>