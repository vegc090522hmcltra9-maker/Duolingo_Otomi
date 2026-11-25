<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "d_o_axel_502";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$email = $_POST['email'];
$pass = $_POST['password'];


$stmt = $conn->prepare("SELECT id_usuario, nombre, email FROM usuario WHERE email = ? AND contraseña = ?");
$stmt->bind_param("ss", $email, $pass);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    
    session_start();
    $_SESSION['id_usuario'] = $row['id_usuario'];
    $_SESSION['nombre'] = $row['nombre'];
    $_SESSION['email'] = $row['email'];
    $_SESSION['usuario'] = $email;

    echo "<script>window.location.href='leccion1.html';</script>";
    exit();
} else {
    echo "<script>alert('Usuario no encontrado'); window.location.href='login.html';</script>";
}
$stmt->close();
$conn->close();
?>