<?php
include("../config/conexion.php");
include("../includes/auth.php");
include("../includes/data.php");
exigir_login();
asegurar_columnas_tareas($conn);

$titulo = trim($_POST["titulo"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$id_materia = (int)($_POST["id_materia"] ?? 0);
$fecha_entrega = $_POST["fecha_entrega"] ?? null;
$prioridad = $_POST["prioridad"] ?? "Media";
$estado = $_POST["estado"] ?? "Pendiente";
$tipo_periodo = trim($_POST["tipo_periodo"] ?? "");
$periodo_texto = trim($_POST["periodo_texto"] ?? "");
$hoy = new DateTime("today");
$fechaValida = $fecha_entrega ? DateTime::createFromFormat("Y-m-d", $fecha_entrega) : false;

if (!$fechaValida || $fechaValida < $hoy || (int)$fechaValida->format("Y") !== (int)$hoy->format("Y")) {
    header("Location: ../app/tareas.php?error=fecha");
    exit;
}

$id_prioridad = 2;
$stmtPrioridad = $conn->prepare("SELECT id_prioridad FROM prioridad WHERE nombre = ? LIMIT 1");
$stmtPrioridad->bind_param("s", $prioridad);
$stmtPrioridad->execute();
$rowPrioridad = $stmtPrioridad->get_result()->fetch_assoc();
if ($rowPrioridad) {
    $id_prioridad = (int)$rowPrioridad["id_prioridad"];
}

$id_estado = 1;
$stmtEstado = $conn->prepare("SELECT id_estado FROM estado WHERE nombre = ? LIMIT 1");
$stmtEstado->bind_param("s", $estado);
$stmtEstado->execute();
$rowEstado = $stmtEstado->get_result()->fetch_assoc();
if ($rowEstado) {
    $id_estado = (int)$rowEstado["id_estado"];
}

$id_periodo = 1;
$resultPeriodo = $conn->query("SELECT id_periodo FROM periodo ORDER BY id_periodo ASC LIMIT 1");
if ($resultPeriodo && $rowPeriodo = $resultPeriodo->fetch_assoc()) {
    $id_periodo = (int)$rowPeriodo["id_periodo"];
}

if ($titulo !== "" && $id_materia && $tipo_periodo !== "" && $periodo_texto !== "") {
    $stmt = $conn->prepare("INSERT INTO actividad (nombre, descripcion, fecha_entrega, id_materia, id_estado, id_prioridad, id_periodo, tipo_periodo_texto, periodo_texto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiiiss", $titulo, $descripcion, $fecha_entrega, $id_materia, $id_estado, $id_prioridad, $id_periodo, $tipo_periodo, $periodo_texto);
    $stmt->execute();
}

header("Location: ../app/tareas.php");
exit;
?>
