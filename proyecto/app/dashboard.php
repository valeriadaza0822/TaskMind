<?php
include("../config/conexion.php");
include("../includes/auth.php");
include("../includes/data.php");
include("../includes/layout.php");
exigir_login();

$usuario = usuario_actual();
$id_usuario = id_usuario_actual();
$materias = obtener_materias($conn, $id_usuario);
$tareas = obtener_tareas($conn, $id_usuario, 100);
$pendientes = contar_tareas_por_estado($tareas, "Pendiente");
$completadas = contar_tareas_por_estado($tareas, "Completada");
$proximas = count(array_filter($tareas, function ($tarea) {
    $dias = dias_restantes($tarea["fecha_entrega"] ?? null);
    return $dias !== null && $dias >= 0 && $dias <= 7 && strtolower($tarea["estado_visual"] ?? "") !== "completada";
}));
$promedio = obtener_indicador_general($conn, $id_usuario, $materias);
$tareasProximas = array_slice(array_filter($tareas, function ($tarea) {
    return strtolower($tarea["estado_visual"] ?? "") === "pendiente";
}), 0, 5);
$tareasCompletadas = array_filter($tareas, function ($tarea) {
    return strtolower($tarea["estado_visual"] ?? "") === "completada";
});
usort($tareasCompletadas, function ($a, $b) {
    return strcmp($b["fecha_completada"] ?? "", $a["fecha_completada"] ?? "");
});
$tareasCompletadas = array_slice($tareasCompletadas, 0, 5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TaskMind</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body class="app-body">
<div class="app-shell">
    <?php sidebar_taskmind("dashboard"); ?>
    <main class="app-main">
        <?php app_header("Hola, " . ($usuario["nombre"] ?? "Valeria"), $usuario); ?>

        <section class="stats-grid">
            <article class="stat-card"><strong><?php echo $pendientes; ?></strong><span>Tareas pendientes</span></article>
            <article class="stat-card"><strong><?php echo $proximas; ?></strong><span>Próximas a vencer</span></article>
            <article class="stat-card"><strong><?php echo $completadas; ?></strong><span>Tareas completadas</span></article>
            <article class="stat-card"><strong><?php echo $promedio; ?>%</strong><span>Indicador académico</span></article>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Próximas a vencer</h2>
                <a href="tareas.php">Ver todas</a>
            </div>
            <div class="task-list">
                <?php foreach ($tareasProximas as $tarea): ?>
                    <a class="list-card" href="tarea_detalle.php?id=<?php echo (int)$tarea["id_tarea"]; ?>">
                        <span class="icon-tile" style="background: <?php echo htmlspecialchars($tarea["materia_color"] ?? "#7554d9"); ?>">T</span>
                        <span>
                            <h3><?php echo htmlspecialchars($tarea["titulo"]); ?></h3>
                            <p><?php echo htmlspecialchars($tarea["materia_nombre"] ?? "Sin materia"); ?> - <?php echo htmlspecialchars($tarea["fecha_entrega"] ?? "Sin fecha"); ?></p>
                        </span>
                        <span class="badge medium"><?php echo etiqueta_fecha($tarea["fecha_entrega"] ?? null); ?></span>
                    </a>
                <?php endforeach; ?>
                <?php if (count($tareasProximas) === 0): ?>
                    <p class="empty-state">No tienes tareas próximas pendientes.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Tareas recientes completadas</h2>
                <a href="tareas.php?filtro=completadas">Ver completadas</a>
            </div>
            <div class="task-list">
                <?php foreach ($tareasCompletadas as $tarea): ?>
                    <a class="list-card" href="tarea_detalle.php?id=<?php echo (int)$tarea["id_tarea"]; ?>">
                        <span class="check-tile">✓</span>
                        <span>
                            <h3><?php echo htmlspecialchars($tarea["titulo"]); ?></h3>
                            <p><?php echo htmlspecialchars($tarea["materia_nombre"] ?? "Sin materia"); ?> - completada</p>
                        </span>
                        <span class="badge done">Completada</span>
                    </a>
                <?php endforeach; ?>
                <?php if (count($tareasCompletadas) === 0): ?>
                    <p class="empty-state">Cuando completes tareas aparecerán aquí con una marca de verificación verde.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>
</body>
</html>
