<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

include("../config/conexion.php");
include("../includes/auth.php");
include("../includes/data.php");
include("../includes/layout.php");
exigir_login();
asegurar_columnas_perfil($conn);

$usuario = usuario_actual();
$id_usuario = id_usuario_actual();
$materias = obtener_materias($conn, $id_usuario);
$tareas = obtener_tareas($conn, $id_usuario, 100);
$tiposPeriodo = obtener_tipos_periodo($conn);
$completadas = contar_tareas_por_estado($tareas, "Completada");
$pendientes = contar_tareas_por_estado($tareas, "Pendiente");

$stmt = $conn->prepare("SELECT u.*, COALESCE(i.nombre, u.institucion_texto) AS institucion_nombre,
                               tp.nombre AS tipo_periodo_nombre
                        FROM usuario u
                        LEFT JOIN institucion i ON i.id_institucion = u.id_institucion
                        LEFT JOIN tipo_periodo tp ON tp.id_tipo_periodo = u.id_tipo_periodo
                        WHERE u.id_usuario = ?
                        LIMIT 1");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$perfil = $stmt->get_result()->fetch_assoc();

$sedes = [];
if (!empty($perfil["id_institucion"])) {
    $stmtSedes = $conn->prepare("SELECT nombre, direccion FROM sede WHERE id_institucion = ? ORDER BY nombre ASC");
    $stmtSedes->bind_param("i", $perfil["id_institucion"]);
    $stmtSedes->execute();
    $sedes = $stmtSedes->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="version" content="<?php echo time(); ?>">
    <title>Mi perfil - TaskMind</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/app.css">
</head>
<body class="app-body">
<div class="app-shell">
    <?php sidebar_taskmind("perfil"); ?>
    <main class="app-main">
        <?php app_header("Mi perfil", $usuario); ?>

        <?php if (isset($_GET["ok"])): ?>
            <div class="notice success">Perfil actualizado correctamente.</div>
        <?php elseif (isset($_GET["error"])): ?>
            <div class="notice error">No se pudo actualizar el perfil. Revisa los datos o la imagen seleccionada.</div>
        <?php endif; ?>

        <section class="profile-grid">
            <article class="profile-card">
                <div class="profile-avatar-wrap">
                    <div class="profile-avatar"><?php echo strtoupper(substr($perfil["nombre"] ?? "V", 0, 1)); ?></div>
                    <button class="profile-edit-button" type="button" aria-label="Editar información" title="Editar información">
                        <i class="fa fa-pencil"></i>
                    </button>
                </div>
                <h2><?php echo htmlspecialchars($perfil["nombre"] ?? "Usuario"); ?></h2>
                <p><?php echo htmlspecialchars($perfil["correo"] ?: "Correo no registrado"); ?></p>
                <span class="badge pending">Registrado: <?php echo htmlspecialchars($perfil["fecha_registro"] ?? "Sin fecha"); ?></span>
            </article>

            <article class="panel">
                <div class="panel-head"><h2>Resumen de actividad</h2></div>
                <div class="stats-grid compact">
                    <article class="stat-card"><strong><?php echo count($materias); ?></strong><span>Materias</span></article>
                    <article class="stat-card"><strong><?php echo count($tareas); ?></strong><span>Tareas</span></article>
                    <article class="stat-card"><strong><?php echo $pendientes; ?></strong><span>Pendientes</span></article>
                    <article class="stat-card"><strong><?php echo $completadas; ?></strong><span>Completadas</span></article>
                </div>
            </article>
        </section>

        <section class="panel profile-edit-panel" id="profileEditPanel" hidden>
            <div class="panel-head"><h2>Editar información</h2></div>
            <form class="profile-form" action="../acciones/actualizar_perfil.php" method="POST">
                <label>
                    Nombre completo
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($perfil["nombre"] ?? ""); ?>" required>
                </label>
                <label>
                    Correo electrónico
                    <input type="email" name="correo" value="<?php echo htmlspecialchars($perfil["correo"] ?? ""); ?>" required>
                </label>
                <label>
                    Institución
                    <input type="text" name="institucion_texto" value="<?php echo htmlspecialchars($perfil["institucion_nombre"] ?? ""); ?>" placeholder="Nombre de tu institución">
                </label>
                <label>
                    Tipo de periodo
                    <select name="id_tipo_periodo">
                        <?php $tipoPeriodoActual = (int)($perfil["id_tipo_periodo"] ?? 0); ?>
                        <option value="">Selecciona un tipo</option>
                        <?php foreach ($tiposPeriodo as $tipoPeriodo): ?>
                            <option value="<?php echo (int)$tipoPeriodo["id_tipo_periodo"]; ?>" <?php echo $tipoPeriodoActual === (int)$tipoPeriodo["id_tipo_periodo"] ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($tipoPeriodo["nombre"]); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    Periodo actual
                    <input type="number" name="semestre_actual" min="1" max="12" value="<?php echo htmlspecialchars($perfil["semestre_actual"] ?? ""); ?>">
                </label>
                <label>
                    Jornada
                    <select name="jornada">
                        <?php $jornadaActual = $perfil["jornada"] ?? ""; ?>
                        <option value="">Selecciona una jornada</option>
                        <option value="Mañana" <?php echo $jornadaActual === "Mañana" ? "selected" : ""; ?>>Mañana</option>
                        <option value="Tarde" <?php echo $jornadaActual === "Tarde" ? "selected" : ""; ?>>Tarde</option>
                        <option value="Noche" <?php echo $jornadaActual === "Noche" ? "selected" : ""; ?>>Noche</option>
                        <option value="Virtual" <?php echo $jornadaActual === "Virtual" ? "selected" : ""; ?>>Virtual</option>
                    </select>
                </label>
                <button class="primary-btn full" type="submit">Guardar cambios</button>
            </form>
        </section>

        <section class="panel">
            <div class="panel-head"><h2>Información personal y académica</h2></div>
            <div class="info-grid">
                <article class="info-card">
                    <span>Institución</span>
                    <strong><?php echo htmlspecialchars($perfil["institucion_nombre"] ?: "Sin institución registrada"); ?></strong>
                </article>
                <article class="info-card">
                    <span>Tipo de periodo</span>
                    <strong><?php echo htmlspecialchars($perfil["tipo_periodo_nombre"] ?: "Sin tipo registrado"); ?></strong>
                </article>
                <article class="info-card">
                    <span>Periodo actual</span>
                    <strong><?php echo htmlspecialchars($perfil["semestre_actual"] ?: "Sin periodo registrado"); ?></strong>
                </article>
                <article class="info-card">
                    <span>Jornada</span>
                    <strong><?php echo htmlspecialchars($perfil["jornada"] ?: "Sin jornada registrada"); ?></strong>
                </article>
                <article class="info-card">
                    <span>Sede</span>
                    <?php if (count($sedes) === 0): ?>
                        <strong>Sin sede registrada</strong>
                    <?php else: ?>
                        <?php foreach ($sedes as $sede): ?>
                            <strong><?php echo htmlspecialchars($sede["nombre"]); ?></strong>
                            <small><?php echo htmlspecialchars($sede["direccion"] ?? "Sin dirección"); ?></small>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </article>
            </div>
        </section>
    </main>
</div>
<script>
    const editButton = document.querySelector(".profile-edit-button");
    const editPanel = document.getElementById("profileEditPanel");

    if (editButton && editPanel) {
        editButton.addEventListener("click", () => {
            editPanel.hidden = !editPanel.hidden;

            if (!editPanel.hidden) {
                editPanel.scrollIntoView({ behavior: "smooth", block: "start" });
            }
        });
    }
</script>
</body>
</html>
