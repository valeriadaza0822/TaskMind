<?php
include("../config/conexion.php");
include("../includes/auth.php");
include("../includes/data.php");
include("../includes/layout.php");
exigir_login();

$usuario = usuario_actual();
$id_usuario = id_usuario_actual();
$materias = obtener_materias($conn, $id_usuario);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materias - TaskMind</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body class="app-body">
<div class="app-shell">
    <?php sidebar_taskmind("materias"); ?>
    <main class="app-main">
        <?php app_header("Mis materias", $usuario); ?>

        <section class="panel">
            <div class="panel-head"><h2>Nueva materia</h2></div>
            <form class="form-inline" action="../acciones/guardar_materia.php" method="POST">
                <input name="nombre" placeholder="Nombre de la materia" class="full" required>
                <textarea name="descripcion" placeholder="Descripción" class="full"></textarea>
                <select name="tipo_periodo" id="tipoPeriodoMateria" class="full" required>
                    <option value="">Tipo de periodo</option>
                    <option value="Semestral">Semestral</option>
                    <option value="Trimestral">Trimestral</option>
                    <option value="Bimestral">Bimestral</option>
                    <option value="Anual">Anual</option>
                </select>
                <select name="periodo_texto" id="periodoTextoMateria" class="full" required>
                    <option value="">Selecciona primero el tipo de periodo</option>
                </select>
                <button class="primary-btn full" type="submit">Guardar materia</button>
            </form>
        </section>

        <section class="panel">
            <div class="panel-head"><h2>Materias registradas</h2></div>
            <div class="subject-list">
                <?php foreach ($materias as $materia): ?>
                <article class="list-card">
                    <span class="icon-tile" style="background: <?php echo htmlspecialchars($materia["color"] ?? "#7554d9"); ?>"><?php echo strtoupper(substr($materia["nombre"], 0, 1)); ?></span>
                    <span>
                        <h3><a href="materia_detalle.php?id=<?php echo (int)$materia["id_materia"]; ?>"><?php echo htmlspecialchars($materia["nombre"]); ?></a></h3>
                        <p><?php echo htmlspecialchars($materia["profesor"] ?? "Sin descripción"); ?><?php if (!empty($materia["tipo_periodo_texto"]) || !empty($materia["periodo_texto"])): ?> - <?php echo htmlspecialchars(trim(($materia["tipo_periodo_texto"] ?? "") . " " . ($materia["periodo_texto"] ?? ""))); ?><?php else: ?> - Semestre <?php echo (int)($materia["semestre"] ?? 1); ?><?php endif; ?></p>
                    </span>
                    <span class="subject-actions">
                        <span class="badge pending"><?php echo (int)($materia["progreso"] ?? 0); ?>%</span>
                        <a class="outline-btn small-btn" href="materia_detalle.php?id=<?php echo (int)$materia["id_materia"]; ?>">Ver tareas</a>
                        <form action="../acciones/eliminar_materia.php" method="POST" onsubmit="return confirm('¿Borrar esta materia y sus tareas?');">
                            <input type="hidden" name="id_materia" value="<?php echo (int)$materia["id_materia"]; ?>">
                            <button class="danger-btn" type="submit">Borrar</button>
                        </form>
                    </span>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</div>
<script>
    const tipoPeriodoMateria = document.getElementById("tipoPeriodoMateria");
    const periodoTextoMateria = document.getElementById("periodoTextoMateria");
    const opcionesPeriodoMateria = {
        Semestral: Array.from({ length: 10 }, (_, i) => `Semestre ${i + 1}`),
        Trimestral: ["Trimestre 1", "Trimestre 2", "Trimestre 3"],
        Bimestral: ["Bimestre 1", "Bimestre 2", "Bimestre 3", "Bimestre 4", "Bimestre 5", "Bimestre 6"],
        Anual: ["Prejardín", "Jardín", "Transición", "1°", "2°", "3°", "4°", "5°", "6°", "7°", "8°", "9°", "10°", "11°"]
    };

    tipoPeriodoMateria.addEventListener("change", () => {
        const opciones = opcionesPeriodoMateria[tipoPeriodoMateria.value] || [];
        periodoTextoMateria.innerHTML = '<option value="">Selecciona periodo o grado</option>';
        opciones.forEach((opcion) => {
            const option = document.createElement("option");
            option.value = opcion;
            option.textContent = opcion;
            periodoTextoMateria.appendChild(option);
        });
    });
</script>
</body>
</html>
