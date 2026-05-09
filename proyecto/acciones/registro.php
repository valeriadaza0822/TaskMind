<?php
include("../config/conexion.php");
include("../includes/data.php");
asegurar_columnas_perfil($conn);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombre = trim($_POST["nombre"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$institucion = trim($_POST["institucion"] ?? "");
$passwordPlano = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";

if ($nombre === "" || $correo === "" || $institucion === "" || $passwordPlano === "" || $passwordPlano !== $confirmPassword) {
    header("Location: ../auth/registro.html?error=campos");
    exit;
}

if (!preg_match("/^[0-9]{6,8}$/", $passwordPlano)) {
    header("Location: ../auth/registro.html?error=password");
    exit;
}

$password = password_hash($passwordPlano, PASSWORD_DEFAULT);

$verificar = $conn->prepare("SELECT * FROM usuario WHERE correo = ? LIMIT 1");
$verificar->bind_param("s", $correo);
$verificar->execute();
$resultado = $verificar->get_result();

if ($resultado->num_rows > 0) {
    header("Location: ../auth/registro.html?error=existe");
    exit;
}

$stmt = $conn->prepare("INSERT INTO usuario (nombre, correo, institucion_texto, contrasena, fecha_registro) VALUES (?, ?, ?, ?, CURDATE())");
$stmt->bind_param("ssss", $nombre, $correo, $institucion, $password);

if (!$stmt->execute()) {
    header("Location: ../auth/registro.html?error=bd");
    exit;
}

$_SESSION["usuario"] = [
    "id_usuario" => $conn->insert_id,
    "nombre" => $nombre,
    "correo" => $correo
];

header("Location: ../app/dashboard.php");
exit;
?>
