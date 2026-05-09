<?php
include("../config/conexion.php");
include("../includes/auth.php");
exigir_login();

$id_usuario = id_usuario_actual();
$id_materia = (int)($_POST["id_materia"] ?? 0);

if ($id_usuario && $id_materia) {
    $stmtCheck = $conn->prepare("SELECT id_materias FROM materias WHERE id_materias = ? AND id_usuario = ? LIMIT 1");
    $stmtCheck->bind_param("ii", $id_materia, $id_usuario);
    $stmtCheck->execute();
    $existe = $stmtCheck->get_result()->fetch_assoc();

    if ($existe) {
        $stmtTareas = $conn->prepare("DELETE FROM actividad WHERE id_materia = ?");
        $stmtTareas->bind_param("i", $id_materia);
        $stmtTareas->execute();

        $stmtMateria = $conn->prepare("DELETE FROM materias WHERE id_materias = ? AND id_usuario = ?");
        $stmtMateria->bind_param("ii", $id_materia, $id_usuario);
        $stmtMateria->execute();
    }
}

header("Location: ../app/materias.php");
exit;
?>
