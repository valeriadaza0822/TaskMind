<?php
include("../config/conexion.php");
include("../includes/auth.php");
include("../includes/data.php");
exigir_login();
asegurar_columnas_perfil($conn);

$id_usuario = id_usuario_actual();
$nombre = trim($_POST["nombre"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$documento = trim($_POST["documento"] ?? "");
$institucion = trim($_POST["institucion_texto"] ?? "");
$programa = trim($_POST["programa_academico"] ?? "");
$semestre = trim($_POST["semestre_actual"] ?? "");
$idTipoPeriodo = trim($_POST["id_tipo_periodo"] ?? "");
$jornada = trim($_POST["jornada"] ?? "");

if ($nombre === "" || $correo === "") {
    header("Location: ../app/perfil.php?error=campos");
    exit;
}

$semestreValor = $semestre === "" ? null : (int)$semestre;
$tipoPeriodoValor = $idTipoPeriodo === "" ? null : (int)$idTipoPeriodo;

$stmt = $conn->prepare("UPDATE usuario
                        SET nombre = ?, correo = ?, telefono = ?, documento = ?,
                            institucion_texto = ?, programa_academico = ?, semestre_actual = ?,
                            id_tipo_periodo = ?, jornada = ?
                        WHERE id_usuario = ?");
$stmt->bind_param("ssssssiisi", $nombre, $correo, $telefono, $documento, $institucion, $programa, $semestreValor, $tipoPeriodoValor, $jornada, $id_usuario);

if (!$stmt || !$stmt->execute()) {
    header("Location: ../app/perfil.php?error=bd");
    exit;
}

$_SESSION["usuario"]["nombre"] = $nombre;
$_SESSION["usuario"]["correo"] = $correo;

header("Location: ../app/perfil.php?ok=1");
exit;
?>
