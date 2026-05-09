<?php
include("../config/conexion.php");
include("../includes/auth.php");
exigir_login();

$id_usuario = id_usuario_actual();
$id_materia = (int)($_POST["id_materia"] ?? 0);
$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$tipo_periodo = trim($_POST["tipo_periodo"] ?? "");
$periodo_texto = trim($_POST["periodo_texto"] ?? "");
$semestre = (int)($_POST["semestre"] ?? 1);

if ($id_usuario && $id_materia && $nombre !== "") {
    $stmt = $conn->prepare("UPDATE materias
                            SET nombre_materia = ?, descripcion = ?, semestre = ?, tipo_periodo_texto = ?, periodo_texto = ?
                            WHERE id_materias = ? AND id_usuario = ?");
    $stmt->bind_param("ssissii", $nombre, $descripcion, $semestre, $tipo_periodo, $periodo_texto, $id_materia, $id_usuario);
    $stmt->execute();
}

header("Location: ../app/materia_detalle.php?id=" . $id_materia);
exit;
?>
