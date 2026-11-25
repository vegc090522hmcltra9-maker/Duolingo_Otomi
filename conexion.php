<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "d_o_axel_502";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>