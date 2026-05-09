<?php
include("../config/conexion.php");
include("../includes/auth.php");
include("../includes/data.php");
include("../includes/layout.php");
exigir_login();

$usuario = usuario_actual();
$id_usuario = id_usuario_actual();
$tareas = obtener_tareas($conn, $id_usuario, 100);
$filtro = $_GET["filtro"] ?? "todas";

$notificaciones = [];
foreach ($tareas as $tarea) {
    $dias = dias_restantes($tarea["fecha_entrega"] ?? null);
    $estado = strtolower($tarea["estado_visual"] ?? $tarea["estado"] ?? "");

    if ($dias === null || $estado === "completada") {
        continue;
    }

    if ($dias < 0) {
        $tipo = "Sin leer";
        $titulo = "Tarea vencida";
        $color = "#ff5c72";
    } elseif ($dias <= 7) {
        $tipo = "Recordatorio";
        $titulo = "Entrega próxima";
        $color = "#ff9f2e";
    } else {
        continue;
    }

    $notificaciones[] = [
        "tipo" => $tipo,
        "titulo" => $titulo,
        "color" => $color,
        "tarea" => $tarea,
    ];
}

$notificacionesFiltradas = array_filter($notificaciones, function ($notificacion) use ($filtro) {
    if ($filtro === "sin_leer") {
        return $notificacion["tipo"] === "Sin leer";
    }
    if ($filtro === "recordatorios") {
        return $notificacion["tipo"] === "Recordatorio";
    }
    return true;
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertas - TaskMind</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body class="app-body">
<div class="app-shell">
    <?php sidebar_taskmind("alertas"); ?>
    <main class="app-main">
        <?php app_header("Alertas y notificaciones", $usuario); ?>
        <section class="panel">
            <div class="panel-head"><h2>Notificaciones</h2></div>
            <nav class="filter-tabs" aria-label="Filtros de notificaciones">
                <a class="<?php echo $filtro === "todas" ? "active" : ""; ?>" href="alertas.php?filtro=todas">Ver todas las notificaciones</a>
                <a class="<?php echo $filtro === "sin_leer" ? "active" : ""; ?>" href="alertas.php?filtro=sin_leer">Sin leer</a>
                <a class="<?php echo $filtro === "recordatorios" ? "active" : ""; ?>" href="alertas.php?filtro=recordatorios">Recordatorios</a>
            </nav>
            <div class="alert-list">
                <?php foreach ($notificacionesFiltradas as $notificacion): ?>
                    <?php $tarea = $notificacion["tarea"]; ?>
                    <article class="list-card">
                        <span class="icon-tile" style="background: <?php echo htmlspecialchars($notificacion["color"]); ?>">!</span>
                        <span>
                            <h3><?php echo htmlspecialchars($notificacion["titulo"]); ?></h3>
                            <p><?php echo htmlspecialchars($tarea["titulo"]); ?> - <?php echo etiqueta_fecha($tarea["fecha_entrega"]); ?></p>
                        </span>
                        <a class="outline-btn small-btn" href="tarea_detalle.php?id=<?php echo (int)$tarea["id_tarea"]; ?>">Ver</a>
                    </article>
                <?php endforeach; ?>
                <?php if (count($notificacionesFiltradas) === 0): ?>
                    <p class="empty-state">No hay notificaciones en este filtro.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>
</body>
</html>
