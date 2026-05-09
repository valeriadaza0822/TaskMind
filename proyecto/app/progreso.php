<?php
include("../config/conexion.php");
include("../includes/auth.php");
include("../includes/data.php");
include("../includes/layout.php");
exigir_login();

$usuario = usuario_actual();
$id_usuario = id_usuario_actual();
$materias = obtener_materias($conn, $id_usuario);
$promedio = obtener_indicador_general($conn, $id_usuario, $materias);
$historialIndicadores = obtener_historial_indicadores($conn, $id_usuario);
$totalMaterias = count($materias);
$mejorMateria = null;
foreach ($materias as $materia) {
    if (!$mejorMateria || (int)$materia["progreso"] > (int)$mejorMateria["progreso"]) {
        $mejorMateria = $materia;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progreso - TaskMind</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body class="app-body">
<div class="app-shell">
    <?php sidebar_taskmind("progreso"); ?>
    <main class="app-main">
        <?php app_header("Mi progreso", $usuario); ?>
        <section class="progress-hero">
            <article class="progress-score" style="--score: <?php echo $promedio; ?>;">
                <div class="score-ring">
                    <strong><?php echo $promedio; ?>%</strong>
                    <span>General</span>
                </div>
                <div>
                    <p class="eyebrow">Indicador académico</p>
                    <h2><?php echo $promedio >= 80 ? "¡Muy bien, " . htmlspecialchars($usuario["nombre"] ?? "Valeria") . "!" : "Vas avanzando"; ?></h2>
                    <p>Este cálculo muestra tu avance general y se apoya en el progreso de cada materia.</p>
                </div>
            </article>
            <article class="progress-mini-card">
                <span>Materias activas</span>
                <strong><?php echo $totalMaterias; ?></strong>
            </article>
            <article class="progress-mini-card">
                <span>Mejor materia</span>
                <strong><?php echo $mejorMateria ? htmlspecialchars($mejorMateria["nombre"]) : "Sin datos"; ?></strong>
            </article>
        </section>

        <section class="panel">
            <div class="panel-head"><h2>Progreso por materia</h2></div>
            <div class="progress-list">
            <?php foreach ($materias as $materia): ?>
                <?php $valor = (int)($materia["progreso"] ?? 0); ?>
                <?php $color = htmlspecialchars($materia["color"] ?? "#7554d9"); ?>
                <article class="progress-subject" style="--subject-color: <?php echo $color; ?>; --subject-score: <?php echo $valor; ?>%;">
                    <div class="progress-subject-head">
                        <span>
                            <strong><?php echo htmlspecialchars($materia["nombre"]); ?></strong>
                            <small><?php echo htmlspecialchars($materia["profesor"] ?? "Sin descripción"); ?></small>
                        </span>
                        <b><?php echo $valor; ?>%</b>
                    </div>
                    <div class="progress-meter" aria-label="Progreso <?php echo $valor; ?>%">
                        <span></span>
                    </div>
                    <p class="subject-advice"><?php echo htmlspecialchars(consejo_por_progreso($valor)); ?></p>
                </article>
            <?php endforeach; ?>
            </div>
        </section>

        <section class="panel">
            <div class="panel-head"><h2>Historial de indicadores del año</h2></div>
            <div class="history-list">
                <?php foreach ($historialIndicadores as $indicador): ?>
                    <article class="history-row">
                        <span>
                            <strong><?php echo htmlspecialchars($indicador["periodo"] ?? "Periodo"); ?></strong>
                            <small><?php echo htmlspecialchars($indicador["fecha_calculo"] ?? "Sin fecha"); ?></small>
                        </span>
                        <b><?php echo round((float)$indicador["porcentaje_cumplimiento"]); ?>%</b>
                    </article>
                <?php endforeach; ?>
                <?php if (count($historialIndicadores) === 0): ?>
                    <p class="empty-state">Aún no hay historial de indicadores registrado para este año.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>
</body>
</html>
