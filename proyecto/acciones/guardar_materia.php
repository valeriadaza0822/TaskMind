<?php
include("../config/conexion.php");
include("../includes/auth.php");
exigir_login();

$id_usuario = id_usuario_actual();
$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$tipo_periodo = trim($_POST["tipo_periodo"] ?? "");
$periodo_texto = trim($_POST["periodo_texto"] ?? "");
$semestre = (int)($_POST["semestre"] ?? 1);

if ($nombre !== "" && $id_usuario) {
    $sql = "INSERT INTO materias (nombre_materia, descripcion, semestre, tipo_periodo_texto, periodo_texto, id_usuario) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissi", $nombre, $descripcion, $semestre, $tipo_periodo, $periodo_texto, $id_usuario);
    $stmt->execute();
}

header("Location: ../app/materias.php");
exit;
?>
