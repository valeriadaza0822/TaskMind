<?php
include("../config/conexion.php");
include("../includes/auth.php");
include("../includes/data.php");
include("../includes/layout.php");
exigir_login();

$usuario = usuario_actual();
$id_usuario = id_usuario_actual();
$tareas = obtener_tareas($conn, $id_usuario, 100);
$mes = max(1, min(12, (int)($_GET["mes"] ?? date("m"))));
$anio = max(2000, min(2100, (int)($_GET["anio"] ?? date("Y"))));
$diasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
$hoy = date("Y-m-d");
$primerDiaMes = sprintf("%04d-%02d-01", $anio, $mes);
$espaciosIniciales = (int)(new DateTime($primerDiaMes))->format("N") - 1;
$mesAnterior = (new DateTime($primerDiaMes))->modify("-1 month");
$mesSiguiente = (new DateTime($primerDiaMes))->modify("+1 month");
$tareasPorDia = [];
$conteoEstados = [
    "pendiente" => 0,
    "vencida" => 0,
    "completada" => 0
];
foreach ($tareas as $tarea) {
    $fecha = $tarea["fecha_entrega"] ?? "";
    if ($fecha && substr($fecha, 0, 7) === sprintf("%04d-%02d", $anio, $mes)) {
        $tareasPorDia[$fecha][] = $tarea;
        $estado = strtolower($tarea["estado_visual"] ?? $tarea["estado"] ?? "pendiente");
        if (isset($conteoEstados[$estado])) {
            $conteoEstados[$estado]++;
        }
    }
}
$totalTareasMes = array_sum(array_map("count", $tareasPorDia));
$diasConTareas = count($tareasPorDia);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario - TaskMind</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/app.css?v=3">
</head>
<body class="app-body calendar-page">
<div class="app-shell">
    <?php sidebar_taskmind("calendario"); ?>
    <main class="app-main">
        <?php app_header("Calendario", $usuario); ?>
        <section class="calendar-hero">
            <div>
                <p class="eyebrow">Plan mensual</p>
                <h2><?php echo nombre_mes_es($mes) . " " . $anio; ?></h2>
                <p>Organiza tus entregas, revisa vencidas, pendientes y completadas, y navega entre meses.</p>
            </div>
            <div class="calendar-summary">
                <article>
                    <span>Tareas del mes</span>
                    <strong><?php echo $totalTareasMes; ?></strong>
                </article>
                <article>
                    <span>Días con entregas</span>
                    <strong><?php echo $diasConTareas; ?></strong>
                </article>
            </div>
        </section>

        <section class="panel calendar-panel">
            <div class="calendar-panel-head">
                <div>
                    <span>Vista mensual</span>
                    <strong><?php echo nombre_mes_es($mes); ?></strong>
                </div>
                <nav class="calendar-nav" aria-label="Cambiar mes">
                    <a href="calendario.php?mes=<?php echo $mesAnterior->format("n"); ?>&anio=<?php echo $mesAnterior->format("Y"); ?>"><?php echo nombre_mes_es((int)$mesAnterior->format("n")); ?></a>
                    <a href="calendario.php?mes=<?php echo $mes; ?>&anio=<?php echo $anio; ?>"><?php echo nombre_mes_es($mes); ?></a>
                    <a href="calendario.php?mes=<?php echo $mesSiguiente->format("n"); ?>&anio=<?php echo $mesSiguiente->format("Y"); ?>"><?php echo nombre_mes_es((int)$mesSiguiente->format("n")); ?></a>
                </nav>
                <div class="calendar-legend">
                    <span><i class="legend-dot today-dot"></i><?php echo nombre_mes_es($mes); ?></span>
                    <span><i class="legend-dot pending-dot"></i>Pendiente</span>
                    <span><i class="legend-dot overdue-dot"></i>Vencida</span>
                    <span><i class="legend-dot done-dot"></i>Completada</span>
                </div>
            </div>
            <div class="calendar-weekdays" aria-hidden="true">
                <span>Lun</span>
                <span>Mar</span>
                <span>Mié</span>
                <span>Jue</span>
                <span>Vie</span>
                <span>Sáb</span>
                <span>Dom</span>
            </div>
            <div class="calendar-grid">
                <?php for ($espacio = 0; $espacio < $espaciosIniciales; $espacio++): ?>
                    <span class="calendar-empty"></span>
                <?php endfor; ?>
                <?php for ($dia = 1; $dia <= $diasMes; $dia++): ?>
                    <?php $fechaDia = sprintf("%04d-%02d-%02d", $anio, $mes, $dia); ?>
                    <?php $tareasDia = $tareasPorDia[$fechaDia] ?? []; ?>
                    <?php
                        $clasesEstadoDia = [];
                        foreach ($tareasDia as $tareaDia) {
                            $clasesEstadoDia[] = clase_estado_tarea($tareaDia["estado_visual"] ?? $tareaDia["estado"] ?? "Pendiente");
                        }
                        $clasesEstadoDia = array_unique($clasesEstadoDia);
                    ?>
                    <button class="calendar-day <?php echo count($tareasDia) > 0 ? "has-tasks" : ""; ?> <?php echo implode(" ", array_map(fn($clase) => "has-" . $clase, $clasesEstadoDia)); ?> <?php echo $fechaDia === $hoy ? "today" : ""; ?>" type="button" data-date="<?php echo $fechaDia; ?>">
                        <span class="calendar-date"><?php echo $dia; ?></span>
                        <?php if (count($tareasDia) > 0): ?>
                            <span class="calendar-count"><?php echo count($tareasDia); ?></span>
                        <?php endif; ?>
                        <?php foreach (array_slice($tareasDia, 0, 2) as $tarea): ?>
                            <span class="calendar-task-dot <?php echo clase_estado_tarea($tarea["estado_visual"] ?? $tarea["estado"] ?? "Pendiente"); ?>"><?php echo htmlspecialchars($tarea["titulo"]); ?></span>
                        <?php endforeach; ?>
                        <?php if (count($tareasDia) > 2): ?>
                            <span class="calendar-more">+<?php echo count($tareasDia) - 2; ?> más</span>
                        <?php endif; ?>
                    </button>
                <?php endfor; ?>
            </div>
        </section>
        <section class="panel day-agenda-panel" id="dayTasksPanel">
            <div class="panel-head"><h2>Agenda del día</h2></div>
            <div class="task-list" id="dayTasks">
                <p class="empty-state">Selecciona un día del calendario para ver sus tareas.</p>
            </div>
        </section>
    </main>
</div>
<script>
    const tareasPorDia = <?php echo json_encode($tareasPorDia, JSON_UNESCAPED_UNICODE); ?>;
    const dayTasks = document.getElementById("dayTasks");
    const fechaLegible = (fecha) => new Date(`${fecha}T00:00:00`).toLocaleDateString("es-CO", {
        weekday: "long",
        day: "numeric",
        month: "long"
    });
    const escapeHtml = (value) => String(value ?? "").replace(/[&<>"']/g, (char) => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;"
    }[char]));

    document.querySelectorAll(".calendar-day").forEach((day) => {
        day.addEventListener("click", () => {
            const fecha = day.dataset.date;
            const tareas = tareasPorDia[fecha] || [];
            document.querySelectorAll(".calendar-day").forEach((item) => item.classList.remove("selected"));
            day.classList.add("selected");

            if (tareas.length === 0) {
                dayTasks.innerHTML = `<p class="empty-state">No hay tareas para ${fechaLegible(fecha)}.</p>`;
                return;
            }

            dayTasks.innerHTML = `
                <div class="day-task-summary">
                    <strong>${fechaLegible(fecha)}</strong>
                    <span>${tareas.length} ${tareas.length === 1 ? "tarea" : "tareas"}</span>
                </div>
                ${tareas.map((tarea) => `
                <a class="list-card" href="tarea_detalle.php?id=${tarea.id_tarea}">
                    <span class="icon-tile" style="background: ${tarea.materia_color || "#7554d9"}">T</span>
                    <span>
                        <h3>${escapeHtml(tarea.titulo)}</h3>
                        <p>${escapeHtml(tarea.materia_nombre || "Sin materia")} - ${fecha}</p>
                    </span>
                    <span class="badge ${tarea.estado_visual === "Completada" ? "done" : (tarea.estado_visual === "Vencida" ? "high" : "pending")}">${escapeHtml(tarea.estado_visual || tarea.estado || "Pendiente")}</span>
                </a>
            `).join("")}`;
        });
    });
</script>
</body>
</html>
