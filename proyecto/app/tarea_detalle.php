<?php
include("../config/conexion.php");
include("../includes/auth.php");
include("../includes/data.php");
include("../includes/layout.php");
exigir_login();

$usuario = usuario_actual();
$id_usuario = id_usuario_actual();
$id_tarea = (int)($_GET["id"] ?? 0);
$tarea = obtener_tarea($conn, $id_usuario, $id_tarea);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de tarea - TaskMind</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body class="app-body">
<div class="app-shell">
    <?php sidebar_taskmind("tareas"); ?>
    <main class="app-main">
        <?php app_header("Detalle de tarea", $usuario); ?>

        <?php if (!$tarea): ?>
            <section class="panel"><p>No se encontró la tarea.</p></section>
        <?php else: ?>
        <?php $estadoVisual = $tarea["estado_visual"] ?? $tarea["estado"]; ?>
        <section class="panel">
            <div class="panel-head">
                <h2><?php echo htmlspecialchars($tarea["titulo"]); ?></h2>
                <a class="outline-btn" href="tareas.php">Volver</a>
            </div>
            <div class="detail-grid">
                <div class="detail-row"><strong>Descripción</strong><span><?php echo htmlspecialchars($tarea["descripcion"] ?: "Sin descripción"); ?></span></div>
                <div class="detail-row"><strong>Fecha</strong><span><?php echo htmlspecialchars($tarea["fecha_entrega"] ?? "Sin fecha"); ?></span></div>
                <div class="detail-row"><strong>Prioridad</strong><span class="badge <?php echo strtolower($tarea["prioridad"]) === "alta" ? "high" : (strtolower($tarea["prioridad"]) === "baja" ? "low" : "medium"); ?>"><?php echo htmlspecialchars($tarea["prioridad"]); ?></span></div>
                <div class="detail-row"><strong>Estado</strong><span class="badge <?php echo clase_estado_tarea($estadoVisual); ?>"><?php echo htmlspecialchars($estadoVisual); ?></span></div>
                <div class="detail-row"><strong>Materia</strong><span><?php echo htmlspecialchars($tarea["materia_nombre"] ?? "Sin materia"); ?></span></div>
                <div class="detail-row"><strong>Periodo</strong><span><?php echo htmlspecialchars(($tarea["tipo_periodo_texto"] ?? "Sin tipo") . " - " . ($tarea["periodo_texto"] ?? "Sin periodo")); ?></span></div>
            </div>

            <form action="../acciones/completar_tarea.php" method="POST" class="complete-box">
                <input type="hidden" name="id_tarea" value="<?php echo (int)$tarea["id_tarea"]; ?>">
                <input type="hidden" name="volver" value="detalle">
                <label>
                    <input type="checkbox" name="completada" value="1" <?php echo strtolower($estadoVisual) === "completada" ? "checked disabled" : ""; ?> onchange="this.form.submit()">
                    <span>Marcar esta tarea como completada</span>
                </label>
            </form>
        </section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
