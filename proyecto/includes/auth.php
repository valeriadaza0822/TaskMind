<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuario_actual() {
    return $_SESSION["usuario"] ?? null;
}

function exigir_login() {
    if (!usuario_actual()) {
        header("Location: ../auth/login.html");
        exit;
    }
}

function id_usuario_actual() {
    $usuario = usuario_actual();
    return $usuario["id_usuario"] ?? $usuario["id"] ?? null;
}
?>
