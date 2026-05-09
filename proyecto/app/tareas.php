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
$filtro = $_GET["filtro"] ?? "todas";
$hoy = date("Y-m-d");
$anioActual = date("Y");

$tareasFiltradas = array_filter($tareas, function ($tarea) use ($filtro) {
    $estado = strtolower($tarea["estado_visual"] ?? $tarea["estado"] ?? "");
    if ($filtro === "pendientes") {
        return $estado === "pendiente";
    }
    if ($filtro === "completadas") {
        return $estado === "completada";
    }
    if ($filtro === "vencidas") {
        return $estado === "vencida";
    }
    return true;
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas - TaskMind</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body class="app-body">
<div class="app-shell">
    <?php sidebar_taskmind("tareas"); ?>
    <main class="app-main">
        <?php app_header("Mis tareas", $usuario); ?>
        <?php if (($_GET["error"] ?? "") === "fecha"): ?>
            <div class="notice error">La fecha debe ser desde hoy y dentro del año <?php echo $anioActual; ?>.</div>
        <?php endif; ?>

        <section class="panel">
            <div class="panel-head"><h2>Nueva tarea</h2></div>
            <form class="form-inline" action="../acciones/guardar_tarea.php" method="POST">
                <input name="titulo" placeholder="Título de la tarea" required>
                <select name="id_materia" required>
                    <option value="">Selecciona materia</option>
                    <?php foreach ($materias as $materia): ?>
                    <option value="<?php echo (int)$materia["id_materia"]; ?>"><?php echo htmlspecialchars($materia["nombre"]); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="fecha_entrega" min="<?php echo $hoy; ?>" max="<?php echo $anioActual; ?>-12-31" required>
                <select name="prioridad">
                    <option>Alta</option>
                    <option selected>Media</option>
                    <option>Baja</option>
                </select>
                <select name="tipo_periodo" id="tipoPeriodo" required>
                    <option value="">Tipo de periodo</option>
                    <option value="Semestral">Semestral</option>
                    <option value="Trimestral">Trimestral</option>
                    <option value="Bimestral">Bimestral</option>
                    <option value="Anual">Anual</option>
                </select>
                <select name="periodo_texto" id="periodoTexto" required>
                    <option value="">Elige primero el tipo</option>
                </select>
                <textarea name="descripcion" placeholder="Descripción" class="full"></textarea>
                <button class="primary-btn full" type="submit">Guardar tarea</button>
            </form>
        </section>

        <section class="panel">
            <div class="panel-head"><h2>Lista de tareas</h2></div>
            <nav class="filter-tabs" aria-label="Filtros de tareas">
                <a class="<?php echo $filtro === "todas" ? "active" : ""; ?>" href="tareas.php?filtro=todas">Todas</a>
                <a class="<?php echo $filtro === "pendientes" ? "active" : ""; ?>" href="tareas.php?filtro=pendientes">Pendientes</a>
                <a class="<?php echo $filtro === "completadas" ? "active" : ""; ?>" href="tareas.php?filtro=completadas">Completadas</a>
                <a class="<?php echo $filtro === "vencidas" ? "active" : ""; ?>" href="tareas.php?filtro=vencidas">Vencidas</a>
            </nav>
            <div class="task-list">
                <?php foreach ($tareasFiltradas as $tarea): ?>
                <?php $estadoVisual = $tarea["estado_visual"] ?? $tarea["estado"]; ?>
                <article class="list-card">
                    <span class="icon-tile" style="background: <?php echo htmlspecialchars($tarea["materia_color"] ?? "#7554d9"); ?>">T</span>
                    <span>
                        <h3><a href="tarea_detalle.php?id=<?php echo (int)$tarea["id_tarea"]; ?>"><?php echo htmlspecialchars($tarea["titulo"]); ?></a></h3>
                        <p><?php echo htmlspecialchars($tarea["materia_nombre"] ?? "Sin materia"); ?> - <?php echo htmlspecialchars($tarea["fecha_entrega"] ?? "Sin fecha"); ?> - <?php echo htmlspecialchars(($tarea["tipo_periodo_texto"] ?? "") . " " . ($tarea["periodo_texto"] ?? "")); ?></p>
                    </span>
                    <span class="badge <?php echo clase_estado_tarea($estadoVisual); ?>"><?php echo htmlspecialchars($estadoVisual); ?></span>
                </article>
                <?php endforeach; ?>
                <?php if (count($tareasFiltradas) === 0): ?>
                    <p class="empty-state">No hay tareas en este filtro.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>
<script>
    const tipoPeriodo = document.getElementById("tipoPeriodo");
    const periodoTexto = document.getElementById("periodoTexto");
    const opcionesPeriodo = {
        Semestral: Array.from({ length: 10 }, (_, i) => `Semestre ${i + 1}`),
        Trimestral: ["Trimestre 1", "Trimestre 2", "Trimestre 3"],
        Bimestral: ["Bimestre 1", "Bimestre 2", "Bimestre 3", "Bimestre 4", "Bimestre 5", "Bimestre 6"],
        Anual: ["Prejardín", "Jardín", "Transición", "1°", "2°", "3°", "4°", "5°", "6°", "7°", "8°", "9°", "10°", "11°"]
    };

    tipoPeriodo.addEventListener("change", () => {
        const opciones = opcionesPeriodo[tipoPeriodo.value] || [];
        periodoTexto.innerHTML = '<option value="">Selecciona periodo o grado</option>';
        opciones.forEach((opcion) => {
            const option = document.createElement("option");
            option.value = opcion;
            option.textContent = opcion;
            periodoTexto.appendChild(option);
        });
    });
</script>
</body>
</html>
