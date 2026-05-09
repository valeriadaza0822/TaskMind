<?php
function ejecutar_consulta($conn, $sql, $types = "", $params = []) {
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    if ($types !== "" && count($params) > 0) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    return $stmt->get_result();
}

function limpiar_texto_visible($texto) {
    if ($texto === null) {
        return "";
    }

    $texto = (string)$texto;

    $texto = str_replace(
        [
            "├í",
            "├®",
            "├¡",
            "├│",
            "├║",
            "├▒",
            "├ü",
            "├ë",
            "├ì",
            "├ô",
            "├Ü",
            "┬¿",
            "┬°"
        ],
        [
            "á",
            "é",
            "í",
            "ó",
            "ú",
            "ñ",
            "Á",
            "É",
            "Í",
            "Ó",
            "Ú",
            "¿",
            "°"
        ],
        $texto
    );

    if (preg_match('/[ÃÂâ]/u', $texto)) {
        $convertido = @mb_convert_encoding($texto, "Windows-1252", "UTF-8");
        if ($convertido !== false && mb_check_encoding($convertido, "UTF-8")) {
            $texto = $convertido;
        }
    }

    if (class_exists("Normalizer")) {
        $normalizado = Normalizer::normalize($texto, Normalizer::FORM_C);
        if ($normalizado !== false) {
            $texto = $normalizado;
        }
    }

    $texto = str_replace(["|", "_", "◌", "�", "፡"], "", $texto);

    if (class_exists("Normalizer")) {
        $normalizado = Normalizer::normalize($texto, Normalizer::FORM_C);
        if ($normalizado !== false) {
            $texto = $normalizado;
        }
    }

    $texto = preg_replace('/[\x{0300}-\x{036F}]+/u', "", $texto);

    $correcciones = [
        "Matematicas" => "Matemáticas",
        "matematicas" => "matemáticas",
        "Pagina" => "Página",
        "pagina" => "página",
        "Diseno" => "Diseño",
        "diseno" => "diseño",
        "Calculo" => "Cálculo",
        "calculo" => "cálculo",
        "Fisica" => "Física",
        "fisica" => "física",
        "Ingles" => "Inglés",
        "ingles" => "inglés",
        "Programacion" => "Programación",
        "programacion" => "programación",
        "Basica" => "Básica",
        "basica" => "básica",
        "Practico" => "Práctico",
        "practico" => "práctico",
        "Teorico" => "Teórico",
        "teorico" => "teórico",
        "Pendulo" => "Péndulo",
        "pendulo" => "péndulo",
        "Resena" => "Reseña",
        "resena" => "reseña",
        "Psicologia" => "Psicología",
        "psicologia" => "psicología",
        "Electronico" => "Electrónico",
        "electronico" => "electrónico",
        "Academico" => "Académico",
        "academico" => "académico",
        "Institucion" => "Institución",
        "institucion" => "institución",
        "Descripcion" => "Descripción",
        "descripcion" => "descripción",
        "Sesion" => "Sesión",
        "sesion" => "sesión",
        "Contrasena" => "Contraseña",
        "contrasena" => "contraseña",
        "Informacion" => "Información",
        "informacion" => "información",
        "Proxima" => "Próxima",
        "proxima" => "próxima",
        "Proximas" => "Próximas",
        "proximas" => "próximas",
        "Dia" => "Día",
        "dia" => "día"
    ];

    foreach ($correcciones as $sinTilde => $conTilde) {
        $patron = '/(?<![\p{L}])' . preg_quote($sinTilde, '/') . '(?![\p{L}])/u';
        $texto = preg_replace($patron, $conTilde, $texto);
    }

    $texto = str_replace(
        ["Medía", "medía", "inglésa", "Inglésa"],
        ["Media", "media", "inglesa", "Inglesa"],
        $texto
    );

    $texto = str_replace(
        [
            "Laboratorio Física",
            "Completaste Laboratorio",
            "Actividad Laboratorio"
        ],
        [
            "Laboratorio de Física",
            "completaste Laboratorio",
            "Tarea Laboratorio"
        ],
        $texto
    );

    return $texto;
}

function columna_existe($conn, $tabla, $columna) {
    $sql = "SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?
            LIMIT 1";
    $result = ejecutar_consulta($conn, $sql, "ss", [$tabla, $columna]);
    return $result && $result->num_rows > 0;
}

function asegurar_columnas_perfil($conn) {
    $columnas = [
        "contrasena" => "ALTER TABLE usuario ADD COLUMN contrasena VARCHAR(255) DEFAULT NULL",
        "telefono" => "ALTER TABLE usuario ADD COLUMN telefono VARCHAR(30) DEFAULT NULL",
        "documento" => "ALTER TABLE usuario ADD COLUMN documento VARCHAR(40) DEFAULT NULL",
        "institucion_texto" => "ALTER TABLE usuario ADD COLUMN institucion_texto VARCHAR(120) DEFAULT NULL",
        "programa_academico" => "ALTER TABLE usuario ADD COLUMN programa_academico VARCHAR(120) DEFAULT NULL",
        "semestre_actual" => "ALTER TABLE usuario ADD COLUMN semestre_actual INT DEFAULT NULL",
        "id_tipo_periodo" => "ALTER TABLE usuario ADD COLUMN id_tipo_periodo INT DEFAULT NULL",
        "jornada" => "ALTER TABLE usuario ADD COLUMN jornada VARCHAR(50) DEFAULT NULL"
    ];

    foreach ($columnas as $columna => $alterSql) {
        if (!columna_existe($conn, "usuario", $columna)) {
            $conn->query($alterSql);
        }
    }
}

function asegurar_columnas_tareas($conn) {
    $columnas = [
        "tipo_periodo_texto" => "ALTER TABLE actividad ADD COLUMN tipo_periodo_texto VARCHAR(30) DEFAULT NULL",
        "periodo_texto" => "ALTER TABLE actividad ADD COLUMN periodo_texto VARCHAR(50) DEFAULT NULL",
        "fecha_completada" => "ALTER TABLE actividad ADD COLUMN fecha_completada DATETIME DEFAULT NULL"
    ];

    foreach ($columnas as $columna => $alterSql) {
        if (!columna_existe($conn, "actividad", $columna)) {
            $conn->query($alterSql);
        }
    }
}

function asegurar_columnas_materias($conn) {
    $columnas = [
        "tipo_periodo_texto" => "ALTER TABLE materias ADD COLUMN tipo_periodo_texto VARCHAR(30) DEFAULT NULL",
        "periodo_texto" => "ALTER TABLE materias ADD COLUMN periodo_texto VARCHAR(50) DEFAULT NULL"
    ];

    foreach ($columnas as $columna => $alterSql) {
        if (!columna_existe($conn, "materias", $columna)) {
            $conn->query($alterSql);
        }
    }
}

function obtener_tipos_periodo($conn) {
    $result = $conn->query("SELECT id_tipo_periodo, nombre FROM tipo_periodo ORDER BY id_tipo_periodo ASC");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function estado_tarea_visual($estado, $fecha) {
    if (strtolower($estado ?? "") === "completada") {
        return "Completada";
    }

    return dias_restantes($fecha) < 0 ? "Vencida" : "Pendiente";
}

function clase_estado_tarea($estado) {
    $estado = strtolower($estado ?? "");
    if ($estado === "completada") {
        return "done";
    }
    if ($estado === "vencida") {
        return "high";
    }
    return "pending";
}

function consejo_por_progreso($valor) {
    $valor = (int)$valor;
    if ($valor >= 85) {
        return "Excelente ritmo. Mantén tus entregas al día y conserva este hábito.";
    }
    if ($valor >= 60) {
        return "Vas bien. Prioriza las próximas entregas y cierra tareas pequeñas esta semana.";
    }
    if ($valor >= 30) {
        return "Necesita atención. Revisa las tareas vencidas y separa bloques cortos de estudio.";
    }
    return "Empieza por una tarea pendiente de esta materia y pide apoyo si hay dudas acumuladas.";
}

function color_materia($id_materia) {
    $colores = ["#30c4a4", "#7554d9", "#ff9f2e", "#77c45d", "#2d8be8", "#ff5c72"];
    return $colores[((int)$id_materia - 1) % count($colores)];
}

function obtener_materias($conn, $id_usuario) {
    asegurar_columnas_materias($conn);
    $sql = "SELECT
                id_materias AS id_materia,
                nombre_materia AS nombre,
                descripcion,
                semestre,
                tipo_periodo_texto,
                periodo_texto
            FROM materias
            WHERE id_usuario = ?
            ORDER BY nombre_materia ASC";
    $result = ejecutar_consulta($conn, $sql, "i", [$id_usuario]);
    $materias = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    foreach ($materias as &$materia) {
        $materia["nombre"] = limpiar_texto_visible($materia["nombre"] ?? "");
        $materia["descripcion"] = limpiar_texto_visible($materia["descripcion"] ?? "");
        $materia["tipo_periodo_texto"] = limpiar_texto_visible($materia["tipo_periodo_texto"] ?? "");
        $materia["periodo_texto"] = limpiar_texto_visible($materia["periodo_texto"] ?? "");
        $materia["color"] = color_materia($materia["id_materia"]);
        $materia["profesor"] = $materia["descripcion"] ?? "Sin descripción";
        $materia["progreso"] = progreso_materia($conn, $materia["id_materia"]);
    }

    return $materias;
}

function progreso_materia($conn, $id_materia) {
    $sql = "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN LOWER(e.nombre) = 'completada' THEN 1 ELSE 0 END) AS completadas
            FROM actividad a
            LEFT JOIN estado e ON e.id_estado = a.id_estado
            WHERE a.id_materia = ?";
    $result = ejecutar_consulta($conn, $sql, "i", [$id_materia]);
    $row = $result ? $result->fetch_assoc() : null;

    if (!$row || (int)$row["total"] === 0) {
        return 0;
    }

    return round(((int)$row["completadas"] / (int)$row["total"]) * 100);
}

function obtener_tareas($conn, $id_usuario, $limite = 50) {
    asegurar_columnas_tareas($conn);
    $sql = "SELECT
                a.id_actividad AS id_tarea,
                a.nombre AS titulo,
                a.descripcion,
                a.fecha_entrega,
                a.tipo_periodo_texto,
                a.periodo_texto,
                a.fecha_completada,
                COALESCE(e.nombre, 'Pendiente') AS estado,
                COALESCE(p.nombre, 'Media') AS prioridad,
                m.id_materias AS id_materia,
                m.nombre_materia AS materia_nombre
            FROM actividad a
            INNER JOIN materias m ON m.id_materias = a.id_materia
            LEFT JOIN estado e ON e.id_estado = a.id_estado
            LEFT JOIN prioridad p ON p.id_prioridad = a.id_prioridad
            WHERE m.id_usuario = ?
            ORDER BY a.fecha_entrega ASC
            LIMIT ?";
    $result = ejecutar_consulta($conn, $sql, "ii", [$id_usuario, $limite]);
    $tareas = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    foreach ($tareas as &$tarea) {
        $tarea["titulo"] = limpiar_texto_visible($tarea["titulo"] ?? "");
        $tarea["descripcion"] = limpiar_texto_visible($tarea["descripcion"] ?? "");
        $tarea["materia_nombre"] = limpiar_texto_visible($tarea["materia_nombre"] ?? "");
        $tarea["tipo_periodo_texto"] = limpiar_texto_visible($tarea["tipo_periodo_texto"] ?? "");
        $tarea["periodo_texto"] = limpiar_texto_visible($tarea["periodo_texto"] ?? "");
        $tarea["materia_color"] = color_materia($tarea["id_materia"]);
        $tarea["estado_visual"] = estado_tarea_visual($tarea["estado"], $tarea["fecha_entrega"]);
    }

    return $tareas;
}

function obtener_tarea($conn, $id_usuario, $id_tarea) {
    asegurar_columnas_tareas($conn);
    $sql = "SELECT
                a.id_actividad AS id_tarea,
                a.nombre AS titulo,
                a.descripcion,
                a.fecha_entrega,
                a.tipo_periodo_texto,
                a.periodo_texto,
                a.fecha_completada,
                COALESCE(e.nombre, 'Pendiente') AS estado,
                COALESCE(p.nombre, 'Media') AS prioridad,
                m.id_materias AS id_materia,
                m.nombre_materia AS materia_nombre
            FROM actividad a
            INNER JOIN materias m ON m.id_materias = a.id_materia
            LEFT JOIN estado e ON e.id_estado = a.id_estado
            LEFT JOIN prioridad p ON p.id_prioridad = a.id_prioridad
            WHERE m.id_usuario = ? AND a.id_actividad = ?";
    $result = ejecutar_consulta($conn, $sql, "ii", [$id_usuario, $id_tarea]);
    $tarea = $result ? $result->fetch_assoc() : null;

    if ($tarea) {
        $tarea["titulo"] = limpiar_texto_visible($tarea["titulo"] ?? "");
        $tarea["descripcion"] = limpiar_texto_visible($tarea["descripcion"] ?? "");
        $tarea["materia_nombre"] = limpiar_texto_visible($tarea["materia_nombre"] ?? "");
        $tarea["tipo_periodo_texto"] = limpiar_texto_visible($tarea["tipo_periodo_texto"] ?? "");
        $tarea["periodo_texto"] = limpiar_texto_visible($tarea["periodo_texto"] ?? "");
        $tarea["materia_color"] = color_materia($tarea["id_materia"]);
        $tarea["estado_visual"] = estado_tarea_visual($tarea["estado"], $tarea["fecha_entrega"]);
    }

    return $tarea;
}

function obtener_materia($conn, $id_usuario, $id_materia) {
    asegurar_columnas_materias($conn);
    $sql = "SELECT
                id_materias AS id_materia,
                nombre_materia AS nombre,
                descripcion,
                semestre,
                tipo_periodo_texto,
                periodo_texto
            FROM materias
            WHERE id_usuario = ? AND id_materias = ?
            LIMIT 1";
    $result = ejecutar_consulta($conn, $sql, "ii", [$id_usuario, $id_materia]);
    $materia = $result ? $result->fetch_assoc() : null;

    if ($materia) {
        $materia["nombre"] = limpiar_texto_visible($materia["nombre"] ?? "");
        $materia["descripcion"] = limpiar_texto_visible($materia["descripcion"] ?? "");
        $materia["tipo_periodo_texto"] = limpiar_texto_visible($materia["tipo_periodo_texto"] ?? "");
        $materia["periodo_texto"] = limpiar_texto_visible($materia["periodo_texto"] ?? "");
        $materia["color"] = color_materia($materia["id_materia"]);
        $materia["profesor"] = $materia["descripcion"] ?? "Sin descripción";
        $materia["progreso"] = progreso_materia($conn, $materia["id_materia"]);
    }

    return $materia;
}

function obtener_tareas_materia($conn, $id_usuario, $id_materia) {
    asegurar_columnas_tareas($conn);
    $sql = "SELECT
                a.id_actividad AS id_tarea,
                a.nombre AS titulo,
                a.descripcion,
                a.fecha_entrega,
                a.tipo_periodo_texto,
                a.periodo_texto,
                a.fecha_completada,
                COALESCE(e.nombre, 'Pendiente') AS estado,
                COALESCE(p.nombre, 'Media') AS prioridad,
                m.id_materias AS id_materia,
                m.nombre_materia AS materia_nombre
            FROM actividad a
            INNER JOIN materias m ON m.id_materias = a.id_materia
            LEFT JOIN estado e ON e.id_estado = a.id_estado
            LEFT JOIN prioridad p ON p.id_prioridad = a.id_prioridad
            WHERE m.id_usuario = ? AND m.id_materias = ?
            ORDER BY a.fecha_entrega ASC";
    $result = ejecutar_consulta($conn, $sql, "ii", [$id_usuario, $id_materia]);
    $tareas = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    foreach ($tareas as &$tarea) {
        $tarea["titulo"] = limpiar_texto_visible($tarea["titulo"] ?? "");
        $tarea["descripcion"] = limpiar_texto_visible($tarea["descripcion"] ?? "");
        $tarea["materia_nombre"] = limpiar_texto_visible($tarea["materia_nombre"] ?? "");
        $tarea["tipo_periodo_texto"] = limpiar_texto_visible($tarea["tipo_periodo_texto"] ?? "");
        $tarea["periodo_texto"] = limpiar_texto_visible($tarea["periodo_texto"] ?? "");
        $tarea["materia_color"] = color_materia($tarea["id_materia"]);
        $tarea["estado_visual"] = estado_tarea_visual($tarea["estado"], $tarea["fecha_entrega"]);
    }

    return $tareas;
}

function contar_tareas_por_estado($tareas, $estado) {
    return count(array_filter($tareas, function ($tarea) use ($estado) {
        return strtolower($tarea["estado_visual"] ?? $tarea["estado"] ?? "") === strtolower($estado);
    }));
}

function dias_restantes($fecha) {
    if (!$fecha) {
        return null;
    }

    $hoy = new DateTime("today");
    $entrega = new DateTime($fecha);
    return (int)$hoy->diff($entrega)->format("%r%a");
}

function etiqueta_fecha($fecha) {
    $dias = dias_restantes($fecha);

    if ($dias === null) {
        return "Sin fecha";
    }

    if ($dias < 0) {
        return "Vencida";
    }

    if ($dias === 0) {
        return "Hoy";
    }

    if ($dias === 1) {
        return "Mañana";
    }

    return $dias . " días";
}

function nombre_mes_es($mes) {
    $meses = [
        1 => "Enero",
        2 => "Febrero",
        3 => "Marzo",
        4 => "Abril",
        5 => "Mayo",
        6 => "Junio",
        7 => "Julio",
        8 => "Agosto",
        9 => "Septiembre",
        10 => "Octubre",
        11 => "Noviembre",
        12 => "Diciembre"
    ];

    return $meses[(int)$mes] ?? "";
}

function promedio_academico($materias) {
    if (count($materias) === 0) {
        return 0;
    }

    $total = 0;
    foreach ($materias as $materia) {
        $total += (int)($materia["progreso"] ?? 0);
    }

    return round($total / count($materias));
}

function obtener_indicador_general($conn, $id_usuario, $materias) {
    $sql = "SELECT porcentaje_cumplimiento
            FROM indicador_academico
            WHERE id_usuario = ?
            ORDER BY fecha_calculo DESC
            LIMIT 1";
    $result = ejecutar_consulta($conn, $sql, "i", [$id_usuario]);
    $row = $result ? $result->fetch_assoc() : null;

    if ($row) {
        return round((float)$row["porcentaje_cumplimiento"]);
    }

    return promedio_academico($materias);
}

function obtener_historial_indicadores($conn, $id_usuario) {
    $sql = "SELECT
                ia.porcentaje_cumplimiento,
                ia.fecha_calculo,
                COALESCE(p.nombre, CONCAT('Periodo ', ia.id_periodo)) AS periodo
            FROM indicador_academico ia
            LEFT JOIN periodo p ON p.id_periodo = ia.id_periodo
            WHERE ia.id_usuario = ?
            AND YEAR(ia.fecha_calculo) = YEAR(CURDATE())
            ORDER BY ia.fecha_calculo DESC";
    $result = ejecutar_consulta($conn, $sql, "i", [$id_usuario]);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>
