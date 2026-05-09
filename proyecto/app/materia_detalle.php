<?php
include("../config/conexion.php");
include("../includes/auth.php");
include("../includes/data.php");
include("../includes/layout.php");
exigir_login();

$usuario = usuario_actual();
$id_usuario = id_usuario_actual();
$id_materia = (int)($_GET["id"] ?? 0);
$materia = obtener_materia($conn, $id_usuario, $id_materia);
$tareas = $materia ? obtener_tareas_materia($conn, $id_usuario, $id_materia) : [];
$pendientes = contar_tareas_por_estado($tareas, "Pendiente");
$completadas = contar_tareas_por_estado($tareas, "Completada");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materia - TaskMind</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body class="app-body">
<div class="app-shell">
    <?php sidebar_taskmind("materias"); ?>
    <main class="app-main">
        <?php app_header("Detalle de materia", $usuario); ?>

        <?php if (!$materia): ?>
            <section class="panel"><p>No se encontró la materia.</p></section>
        <?php else: ?>
            <section class="subject-detail-hero" style="--subject-color: <?php echo htmlspecialchars($materia["color"]); ?>;">
                <div class="subject-mark"><?php echo strtoupper(substr($materia["nombre"], 0, 1)); ?></div>
                <div>
                    <p class="eyebrow">Materia</p>
                    <h2><?php echo htmlspecialchars($materia["nombre"]); ?></h2>
                    <p><?php echo htmlspecialchars($materia["descripcion"] ?? "Sin descripción"); ?></p>
                    <?php if (!empty($materia["tipo_periodo_texto"]) || !empty($materia["periodo_texto"])): ?>
                        <p><strong><?php echo htmlspecialchars(trim(($materia["tipo_periodo_texto"] ?? "") . " " . ($materia["periodo_texto"] ?? ""))); ?></strong></p>
                    <?php endif; ?>
                </div>
                <div class="subject-detail-stats">
                    <span><strong><?php echo count($tareas); ?></strong> tareas</span>
                    <span><strong><?php echo $pendientes; ?></strong> pendientes</span>
                    <span><strong><?php echo $completadas; ?></strong> completadas</span>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><h2>Editar datos de la materia</h2></div>
                <form class="form-inline" action="../acciones/actualizar_materia.php" method="POST">
                    <input type="hidden" name="id_materia" value="<?php echo (int)$materia["id_materia"]; ?>">
                    <input name="nombre" value="<?php echo htmlspecialchars($materia["nombre"]); ?>" placeholder="Nombre de la materia" required>
                    <select name="tipo_periodo" id="tipoPeriodoMateriaEdit" required>
                        <option value="">Tipo de periodo</option>
                        <option value="Semestral" <?php echo $materia["tipo_periodo_texto"] === "Semestral" ? "selected" : ""; ?>>Semestral</option>
                        <option value="Trimestral" <?php echo $materia["tipo_periodo_texto"] === "Trimestral" ? "selected" : ""; ?>>Trimestral</option>
                        <option value="Bimestral" <?php echo $materia["tipo_periodo_texto"] === "Bimestral" ? "selected" : ""; ?>>Bimestral</option>
                        <option value="Anual" <?php echo $materia["tipo_periodo_texto"] === "Anual" ? "selected" : ""; ?>>Anual</option>
                    </select>
                    <select name="periodo_texto" id="periodoTextoMateriaEdit" required>
                        <option value="">Selecciona periodo o grado</option>
                    </select>
                    <input type="number" name="semestre" min="1" max="12" value="<?php echo (int)$materia["semestre"]; ?>" placeholder="Semestre" required>
                    <textarea name="descripcion" class="full" placeholder="Descripción"><?php echo htmlspecialchars($materia["descripcion"] ?? ""); ?></textarea>
                    <button class="primary-btn full" type="submit">Guardar cambios</button>
                </form>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <h2>Tareas de esta materia</h2>
                    <a class="outline-btn" href="tareas.php">Nueva tarea</a>
                </div>
                <div class="task-list">
                    <?php if (count($tareas) === 0): ?>
                        <p>No hay tareas registradas para esta materia.</p>
                    <?php endif; ?>
                    <?php foreach ($tareas as $tarea): ?>
                    <article class="list-card">
                        <span class="icon-tile" style="background: <?php echo htmlspecialchars($tarea["materia_color"]); ?>">T</span>
                        <span>
                            <h3><a href="tarea_detalle.php?id=<?php echo (int)$tarea["id_tarea"]; ?>"><?php echo htmlspecialchars($tarea["titulo"]); ?></a></h3>
                            <p><?php echo htmlspecialchars($tarea["fecha_entrega"]); ?> - <?php echo htmlspecialchars($tarea["estado"]); ?></p>
                        </span>
                        <span class="badge <?php echo strtolower($tarea["prioridad"]) === "alta" ? "high" : (strtolower($tarea["prioridad"]) === "baja" ? "low" : "medium"); ?>">
                            <?php echo htmlspecialchars($tarea["prioridad"]); ?>
                        </span>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
</div>
<script>
    const tipoPeriodoMateriaEdit = document.getElementById("tipoPeriodoMateriaEdit");
    const periodoTextoMateriaEdit = document.getElementById("periodoTextoMateriaEdit");
    const opcionesPeriodoMateria = {
        Semestral: Array.from({ length: 10 }, (_, i) => `Semestre ${i + 1}`),
        Trimestral: ["Trimestre 1", "Trimestre 2", "Trimestre 3"],
        Bimestral: ["Bimestre 1", "Bimestre 2", "Bimestre 3", "Bimestre 4", "Bimestre 5", "Bimestre 6"],
        Anual: ["Prejardín", "Jardín", "Transición", "1°", "2°", "3°", "4°", "5°", "6°", "7°", "8°", "9°", "10°", "11°"]
    };

    function cargarOpcionesPeriodoMateriaEdit() {
        const tipo = tipoPeriodoMateriaEdit.value;
        const opciones = opcionesPeriodoMateria[tipo] || [];
        periodoTextoMateriaEdit.innerHTML = '<option value="">Selecciona periodo o grado</option>';
        opciones.forEach((opcion) => {
            const option = document.createElement("option");
            option.value = opcion;
            option.textContent = opcion;
            if (opcion === <?php echo json_encode($materia["periodo_texto"] ?? ""); ?>) {
                option.selected = true;
            }
            periodoTextoMateriaEdit.appendChild(option);
        });
    }

    tipoPeriodoMateriaEdit.addEventListener("change", () => {
        cargarOpcionesPeriodoMateriaEdit();
    });

    if (tipoPeriodoMateriaEdit.value) {
        cargarOpcionesPeriodoMateriaEdit();
    }
</script>
</body>
</html>
