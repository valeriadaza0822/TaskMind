<?php
function nav_activo($pagina, $actual) {
    return $pagina === $actual ? "active" : "";
}

function sidebar_taskmind($actual) {
?>
<aside class="app-sidebar">
    <a class="app-logo" href="dashboard.php">Task<span>Mind</span></a>
    <nav class="app-nav">
        <a class="<?php echo nav_activo('dashboard', $actual); ?>" href="dashboard.php">Inicio</a>
        <a class="<?php echo nav_activo('materias', $actual); ?>" href="materias.php">Materias</a>
        <a class="<?php echo nav_activo('tareas', $actual); ?>" href="tareas.php">Tareas</a>
        <a class="<?php echo nav_activo('calendario', $actual); ?>" href="calendario.php">Calendario</a>
        <a class="<?php echo nav_activo('alertas', $actual); ?>" href="alertas.php">Alertas</a>
        <a class="<?php echo nav_activo('progreso', $actual); ?>" href="progreso.php">Progreso</a>
        <a class="<?php echo nav_activo('perfil', $actual); ?>" href="perfil.php">Mi perfil</a>
        <a href="../index.html">Página principal</a>
    </nav>
    <a class="logout-link" href="../acciones/logout.php">Cerrar sesión</a>
</aside>
<?php
}

function app_header($titulo, $usuario) {
?>
<header class="app-topbar">
    <div>
        <p class="eyebrow">TaskMind</p>
        <h1><?php echo htmlspecialchars($titulo); ?></h1>
    </div>
    <a class="profile-pill" href="perfil.php">
        <span>Hola, <?php echo htmlspecialchars($usuario["nombre"] ?? "Valeria"); ?></span>
        <strong><?php echo strtoupper(substr($usuario["nombre"] ?? "V", 0, 1)); ?></strong>
    </a>
</header>
<?php
}
?>
