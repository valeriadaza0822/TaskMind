<?php
include("config/conexion.php");
include("includes/data.php");

function nombre_sql($nombre) {
    return "`" . str_replace("`", "``", $nombre) . "`";
}

function columna_visible($columna) {
    $columna = strtolower($columna);
    $omitidas = [
        "correo",
        "contrasena",
        "contraseña",
        "password",
        "telefono",
        "documento",
        "tabla_afectada"
    ];

    foreach ($omitidas as $omitida) {
        if (strpos($columna, $omitida) !== false) {
            return false;
        }
    }

    return true;
}

function clave_primaria($conn, $tabla) {
    $sql = "SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND CONSTRAINT_NAME = 'PRIMARY'
            ORDER BY ORDINAL_POSITION";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tabla);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    return count($resultado) === 1 ? $resultado[0]["COLUMN_NAME"] : null;
}

$sqlColumnas = "SELECT TABLE_NAME, COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND DATA_TYPE IN ('char', 'varchar', 'text', 'mediumtext', 'longtext')
                ORDER BY TABLE_NAME, ORDINAL_POSITION";
$columnas = $conn->query($sqlColumnas)->fetch_all(MYSQLI_ASSOC);

$actualizados = 0;
$revisados = 0;
$tablasSaltadas = [];

foreach ($columnas as $columnaInfo) {
    $tabla = $columnaInfo["TABLE_NAME"];
    $columna = $columnaInfo["COLUMN_NAME"];

    if (!columna_visible($columna)) {
        continue;
    }

    $pk = clave_primaria($conn, $tabla);
    if (!$pk) {
        $tablasSaltadas[$tabla] = true;
        continue;
    }

    $selectSql = "SELECT " . nombre_sql($pk) . " AS id_registro, " . nombre_sql($columna) . " AS texto_original
                  FROM " . nombre_sql($tabla) . "
                  WHERE " . nombre_sql($columna) . " IS NOT NULL";
    $resultado = $conn->query($selectSql);
    if (!$resultado) {
        continue;
    }

    $updateSql = "UPDATE " . nombre_sql($tabla) . "
                  SET " . nombre_sql($columna) . " = ?
                  WHERE " . nombre_sql($pk) . " = ?";
    $stmtUpdate = $conn->prepare($updateSql);

    while ($fila = $resultado->fetch_assoc()) {
        $revisados++;
        $original = $fila["texto_original"];
        $limpio = limpiar_texto_visible($original);

        if ($limpio !== $original) {
            $id = (string)$fila["id_registro"];
            $stmtUpdate->bind_param("ss", $limpio, $id);
            $stmtUpdate->execute();
            $actualizados++;
            echo "{$tabla}.{$columna} #{$id}: {$original} -> {$limpio}" . PHP_EOL;
        }
    }
}

echo PHP_EOL;
echo "Registros revisados: {$revisados}" . PHP_EOL;
echo "Textos actualizados: {$actualizados}" . PHP_EOL;

if (count($tablasSaltadas) > 0) {
    echo "Tablas sin clave primaria simple omitidas: " . implode(", ", array_keys($tablasSaltadas)) . PHP_EOL;
}

$sospechosos = 0;
foreach ($columnas as $columnaInfo) {
    $tabla = $columnaInfo["TABLE_NAME"];
    $columna = $columnaInfo["COLUMN_NAME"];

    if (!columna_visible($columna)) {
        continue;
    }

    $pk = clave_primaria($conn, $tabla);
    if (!$pk) {
        continue;
    }

    $selectSql = "SELECT " . nombre_sql($pk) . " AS id_registro, " . nombre_sql($columna) . " AS texto_original
                  FROM " . nombre_sql($tabla) . "
                  WHERE " . nombre_sql($columna) . " IS NOT NULL";
    $resultado = $conn->query($selectSql);
    if (!$resultado) {
        continue;
    }

    while ($fila = $resultado->fetch_assoc()) {
        $texto = (string)$fila["texto_original"];
        if (preg_match('/[ÃÂâ�|_◌፡\x{0300}-\x{036F}]/u', $texto)) {
            $sospechosos++;
            echo "Pendiente de revisar {$tabla}.{$columna} #{$fila["id_registro"]}: {$texto}" . PHP_EOL;
        }
    }
}

echo "Textos sospechosos restantes: {$sospechosos}" . PHP_EOL;
?>
