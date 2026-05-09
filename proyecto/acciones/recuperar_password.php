<?php
include("../config/conexion.php");
include("../includes/data.php");
asegurar_columnas_perfil($conn);

$correo = trim($_POST["correo"] ?? "");
$passwordPlano = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";

if ($correo === "" || $passwordPlano === "" || $passwordPlano !== $confirmPassword) {
    header("Location: ../auth/recuperar.html?error=campos");
    exit;
}

if (!preg_match("/^[0-9]{6,8}$/", $passwordPlano)) {
    header("Location: ../auth/recuperar.html?error=password");
    exit;
}

$verificar = $conn->prepare("SELECT id_usuario FROM usuario WHERE correo = ? LIMIT 1");
$verificar->bind_param("s", $correo);
$verificar->execute();
$resultado = $verificar->get_result();

if ($resultado->num_rows === 0) {
    header("Location: ../auth/recuperar.html?error=usuario");
    exit;
}

$password = password_hash($passwordPlano, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE usuario SET contrasena = ? WHERE correo = ?");
$stmt->bind_param("ss", $password, $correo);

if (!$stmt->execute()) {
    header("Location: ../auth/recuperar.html?error=bd");
    exit;
}

header("Location: ../auth/login.html?error=recuperada");
exit;
?>
