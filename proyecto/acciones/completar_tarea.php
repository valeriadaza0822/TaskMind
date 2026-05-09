<?php
include("../config/conexion.php");
include("../includes/auth.php");
include("../includes/data.php");
exigir_login();
asegurar_columnas_tareas($conn);

$id_usuario = id_usuario_actual();
$id_tarea = (int)($_POST["id_tarea"] ?? 0);
$volver = $_POST["volver"] ?? "tareas";
$id_estado = 2;

$resultEstado = $conn->query("SELECT id_estado FROM estado WHERE nombre = 'Completada' ORDER BY id_estado ASC LIMIT 1");
if ($resultEstado && $rowEstado = $resultEstado->fetch_assoc()) {
    $id_estado = (int)$rowEstado["id_estado"];
}

if ($id_usuario && $id_tarea && obtener_tarea($conn, $id_usuario, $id_tarea)) {
    $stmt = $conn->prepare("UPDATE actividad SET id_estado = ?, fecha_completada = NOW() WHERE id_actividad = ?");
    $stmt->bind_param("ii", $id_estado, $id_tarea);
    $stmt->execute();
}

if ($volver === "detalle" && $id_tarea) {
    header("Location: ../app/tarea_detalle.php?id=" . $id_tarea);
    exit;
}

header("Location: ../app/tareas.php");
exit;
?>
