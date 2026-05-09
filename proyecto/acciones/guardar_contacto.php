<?php
include("../config/conexion.php");

$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$tipo = $_POST['tipo'];
$mensaje = $_POST['mensaje'];

$sql = "INSERT INTO contacto (nombre, correo, tipo_mensaje, mensaje, fecha_envio)
VALUES ('$nombre', '$correo', '$tipo', '$mensaje', CURDATE())";

if ($conn->query($sql) === TRUE) {
    echo "Mensaje enviado correctamente";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
