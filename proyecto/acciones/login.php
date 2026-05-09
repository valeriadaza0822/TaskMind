<?php
include("../config/conexion.php");
include("../includes/data.php");
asegurar_columnas_perfil($conn);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$correo = trim($_POST["correo"] ?? "");
$password = $_POST["password"] ?? "";

if ($correo === "" || $password === "") {
    header("Location: ../auth/login.html?error=campos");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuario WHERE correo = ? OR nombre = ? LIMIT 1");
$stmt->bind_param("ss", $correo, $correo);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

$passwordGuardado = $usuario ? ($usuario["contrasena"] ?? $usuario["contraseña"] ?? $usuario["password"] ?? "") : "";
$passwordValido = $usuario && (password_verify($password, $passwordGuardado) || hash_equals($passwordGuardado, $password));

if (!$usuario || !$passwordValido) {
    header("Location: ../auth/login.html?error=credenciales");
    exit;
}

$_SESSION["usuario"] = [
    "id_usuario" => $usuario["id_usuario"] ?? $usuario["id"] ?? null,
    "nombre" => $usuario["nombre"],
    "correo" => $usuario["correo"]
];

header("Location: ../app/dashboard.php");
exit;
?>
