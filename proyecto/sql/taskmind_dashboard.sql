CREATE DATABASE IF NOT EXISTS taskmind;
USE taskmind;

DROP VIEW IF EXISTS vista_actividades_estudiantes;
DROP PROCEDURE IF EXISTS calcular_indicador;
DROP FUNCTION IF EXISTS calcular_estado_actividad;

CREATE TABLE IF NOT EXISTS contacto (
    id_contacto INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) DEFAULT NULL,
    correo VARCHAR(100) DEFAULT NULL,
    tipo_mensaje VARCHAR(50) DEFAULT NULL,
    mensaje TEXT,
    fecha_envio DATE DEFAULT NULL,
    PRIMARY KEY (id_contacto)
);

CREATE TABLE IF NOT EXISTS estado (
    id_estado INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_estado)
);

CREATE TABLE IF NOT EXISTS estado_alerta (
    id_estado_alerta INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_estado_alerta)
);

CREATE TABLE IF NOT EXISTS estado_general (
    id_estado INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) DEFAULT NULL,
    PRIMARY KEY (id_estado)
);

CREATE TABLE IF NOT EXISTS nivel_riesgo (
    id_nivel_riesgo INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_nivel_riesgo)
);

CREATE TABLE IF NOT EXISTS prioridad (
    id_prioridad INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_prioridad)
);

CREATE TABLE IF NOT EXISTS tipo_periodo (
    id_tipo_periodo INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_tipo_periodo)
);

CREATE TABLE IF NOT EXISTS institucion (
    id_institucion INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    id_tipo_periodo INT DEFAULT NULL,
    PRIMARY KEY (id_institucion),
    KEY fk_tipo_periodo_idx (id_tipo_periodo),
    CONSTRAINT fk_tipo_periodo FOREIGN KEY (id_tipo_periodo) REFERENCES tipo_periodo (id_tipo_periodo)
);

CREATE TABLE IF NOT EXISTS periodo (
    id_periodo INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE DEFAULT NULL,
    id_institucion INT DEFAULT NULL,
    PRIMARY KEY (id_periodo),
    KEY fk_id_institucion_periodo_idx (id_institucion),
    CONSTRAINT fk_periodo_institucion FOREIGN KEY (id_institucion) REFERENCES institucion (id_institucion)
);

CREATE TABLE IF NOT EXISTS usuario (
    id_usuario INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    `contraseña` VARCHAR(100) NOT NULL,
    fecha_registro DATE DEFAULT NULL,
    id_institucion INT DEFAULT NULL,
    correo VARCHAR(100) NOT NULL,
    id_estado INT DEFAULT NULL,
    telefono VARCHAR(30) DEFAULT NULL,
    documento VARCHAR(40) DEFAULT NULL,
    institucion_texto VARCHAR(120) DEFAULT NULL,
    programa_academico VARCHAR(120) DEFAULT NULL,
    semestre_actual INT DEFAULT NULL,
    jornada VARCHAR(50) DEFAULT NULL,
    PRIMARY KEY (id_usuario),
    KEY fecha_registro (fecha_registro),
    KEY fk_id_institucion_usuario_idx (id_institucion),
    KEY id_estado (id_estado),
    CONSTRAINT fk_ususario_institucion FOREIGN KEY (id_institucion) REFERENCES institucion (id_institucion),
    CONSTRAINT usuario_ibfk_1 FOREIGN KEY (id_estado) REFERENCES estado_general (id_estado)
);

CREATE TABLE IF NOT EXISTS materias (
    id_materias INT NOT NULL AUTO_INCREMENT,
    nombre_materia VARCHAR(100) NOT NULL,
    descripcion TEXT,
    semestre INT NOT NULL,
    id_usuario INT DEFAULT NULL,
    PRIMARY KEY (id_materias),
    KEY id_usuario_idx (id_usuario),
    CONSTRAINT fk_materia_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
);

CREATE TABLE IF NOT EXISTS sede (
    id_sede INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(150) DEFAULT NULL,
    id_institucion INT NOT NULL,
    PRIMARY KEY (id_sede),
    KEY fk_sede_institucion (id_institucion),
    CONSTRAINT fk_sede_institucion FOREIGN KEY (id_institucion) REFERENCES institucion (id_institucion)
);

CREATE TABLE IF NOT EXISTS actividad (
    id_actividad INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_entrega DATE NOT NULL,
    tipo_periodo_texto VARCHAR(30) DEFAULT NULL,
    periodo_texto VARCHAR(50) DEFAULT NULL,
    fecha_completada DATETIME DEFAULT NULL,
    id_materia INT DEFAULT NULL,
    id_estado INT DEFAULT NULL,
    id_prioridad INT DEFAULT NULL,
    id_periodo INT NOT NULL,
    PRIMARY KEY (id_actividad),
    KEY fk_actividad_materia_idx (id_materia),
    KEY fk_actividad_estado_idx (id_estado),
    KEY fk_actividad_prioridad_idx (id_prioridad),
    KEY fk_actitvidad_periodo_idx (id_periodo),
    CONSTRAINT fk_actitvidad_periodo FOREIGN KEY (id_periodo) REFERENCES periodo (id_periodo),
    CONSTRAINT fk_actividad_estado FOREIGN KEY (id_estado) REFERENCES estado (id_estado),
    CONSTRAINT fk_actividad_materia FOREIGN KEY (id_materia) REFERENCES materias (id_materias),
    CONSTRAINT fk_actividad_prioridad FOREIGN KEY (id_prioridad) REFERENCES prioridad (id_prioridad)
);

CREATE TABLE IF NOT EXISTS alerta (
    id_alertas INT NOT NULL AUTO_INCREMENT,
    mensaje TEXT NOT NULL,
    fecha_alerta DATETIME NOT NULL,
    id_estado_alerta INT DEFAULT NULL,
    id_actividad INT DEFAULT NULL,
    PRIMARY KEY (id_alertas),
    KEY fk_alerta_estado_alerta_idx (id_estado_alerta),
    KEY fk_alerta_actividad_idx (id_actividad),
    CONSTRAINT fk_alerta_actividad FOREIGN KEY (id_actividad) REFERENCES actividad (id_actividad),
    CONSTRAINT fk_alerta_estado_alerta FOREIGN KEY (id_estado_alerta) REFERENCES estado_alerta (id_estado_alerta)
);

CREATE TABLE IF NOT EXISTS historial_actividad (
    id_historial INT NOT NULL AUTO_INCREMENT,
    id_actividad INT DEFAULT NULL,
    accion VARCHAR(50) DEFAULT NULL,
    fecha DATETIME DEFAULT NULL,
    PRIMARY KEY (id_historial)
);

CREATE TABLE IF NOT EXISTS historico_usuario (
    id_historico INT NOT NULL AUTO_INCREMENT,
    id_usuario INT DEFAULT NULL,
    tabla_afectada VARCHAR(50) DEFAULT NULL,
    accion VARCHAR(20) DEFAULT NULL,
    descripcion TEXT,
    fecha DATETIME DEFAULT NULL,
    PRIMARY KEY (id_historico),
    KEY id_usuario (id_usuario),
    CONSTRAINT historico_usuario_ibfk_1 FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
);

CREATE TABLE IF NOT EXISTS indicador_academico (
    id_indicador_academico INT NOT NULL AUTO_INCREMENT,
    porcentaje_cumplimiento DECIMAL(5,2) NOT NULL,
    fecha_calculo DATE NOT NULL,
    id_usuario INT DEFAULT NULL,
    id_periodo INT DEFAULT NULL,
    id_nivel INT DEFAULT NULL,
    PRIMARY KEY (id_indicador_academico),
    KEY fk_indicador_academico_usuario_idx (id_usuario),
    KEY fk_indicador_academico_periodo_idx (id_periodo),
    KEY fk_indicador_academico_nivel_riesgo_idx (id_nivel),
    CONSTRAINT fk_indicador_academico_nivel_riesgo FOREIGN KEY (id_nivel) REFERENCES nivel_riesgo (id_nivel_riesgo),
    CONSTRAINT fk_indicador_academico_periodo FOREIGN KEY (id_periodo) REFERENCES periodo (id_periodo),
    CONSTRAINT fk_indicador_academico_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
);

INSERT INTO estado (nombre)
SELECT 'Pendiente' WHERE NOT EXISTS (SELECT 1 FROM estado WHERE nombre = 'Pendiente');
INSERT INTO estado (nombre)
SELECT 'Completada' WHERE NOT EXISTS (SELECT 1 FROM estado WHERE nombre = 'Completada');
INSERT INTO estado (nombre)
SELECT 'Vencida' WHERE NOT EXISTS (SELECT 1 FROM estado WHERE nombre = 'Vencida');

INSERT INTO estado_alerta (nombre)
SELECT 'Sin leer' WHERE NOT EXISTS (SELECT 1 FROM estado_alerta WHERE nombre = 'Sin leer');
INSERT INTO estado_alerta (nombre)
SELECT 'Leída' WHERE NOT EXISTS (SELECT 1 FROM estado_alerta WHERE nombre = 'Leída');

INSERT INTO estado_general (nombre)
SELECT 'Activo' WHERE NOT EXISTS (SELECT 1 FROM estado_general WHERE nombre = 'Activo');
INSERT INTO estado_general (nombre)
SELECT 'Inactivo' WHERE NOT EXISTS (SELECT 1 FROM estado_general WHERE nombre = 'Inactivo');

INSERT INTO prioridad (nombre)
SELECT 'Alta' WHERE NOT EXISTS (SELECT 1 FROM prioridad WHERE nombre = 'Alta');
INSERT INTO prioridad (nombre)
SELECT 'Media' WHERE NOT EXISTS (SELECT 1 FROM prioridad WHERE nombre = 'Media');
INSERT INTO prioridad (nombre)
SELECT 'Baja' WHERE NOT EXISTS (SELECT 1 FROM prioridad WHERE nombre = 'Baja');

INSERT INTO nivel_riesgo (nombre)
SELECT 'Bajo' WHERE NOT EXISTS (SELECT 1 FROM nivel_riesgo WHERE nombre = 'Bajo');
INSERT INTO nivel_riesgo (nombre)
SELECT 'Medio' WHERE NOT EXISTS (SELECT 1 FROM nivel_riesgo WHERE nombre = 'Medio');
INSERT INTO nivel_riesgo (nombre)
SELECT 'Alto' WHERE NOT EXISTS (SELECT 1 FROM nivel_riesgo WHERE nombre = 'Alto');

INSERT INTO tipo_periodo (nombre)
SELECT 'Semestral' WHERE NOT EXISTS (SELECT 1 FROM tipo_periodo WHERE nombre = 'Semestral');
INSERT INTO tipo_periodo (nombre)
SELECT 'Trimestral' WHERE NOT EXISTS (SELECT 1 FROM tipo_periodo WHERE nombre = 'Trimestral');
INSERT INTO tipo_periodo (nombre)
SELECT 'Bimestral' WHERE NOT EXISTS (SELECT 1 FROM tipo_periodo WHERE nombre = 'Bimestral');
INSERT INTO tipo_periodo (nombre)
SELECT 'Anual' WHERE NOT EXISTS (SELECT 1 FROM tipo_periodo WHERE nombre = 'Anual');

INSERT INTO institucion (nombre, id_tipo_periodo)
SELECT 'Institución principal', id_tipo_periodo
FROM tipo_periodo
WHERE nombre = 'Semestral'
AND NOT EXISTS (SELECT 1 FROM institucion WHERE nombre = 'Institución principal')
LIMIT 1;

INSERT INTO periodo (id_periodo, nombre, fecha_inicio, fecha_fin, id_institucion)
SELECT 1, '2026-1', '2026-01-01', '2026-06-30', id_institucion
FROM institucion
WHERE NOT EXISTS (SELECT 1 FROM periodo WHERE id_periodo = 1)
LIMIT 1;

DELIMITER //

CREATE FUNCTION calcular_estado_actividad(fecha_entrega DATE, id_estado INT)
RETURNS VARCHAR(20)
DETERMINISTIC
BEGIN
    DECLARE resultado VARCHAR(20);

    IF id_estado = 2 THEN
        SET resultado = 'Completada';
    ELSEIF fecha_entrega < CURDATE() THEN
        SET resultado = 'Vencida';
    ELSE
        SET resultado = 'Pendiente';
    END IF;

    RETURN resultado;
END//

CREATE PROCEDURE calcular_indicador(IN idUser INT, IN idPeriodo INT)
BEGIN
    DECLARE total INT DEFAULT 0;
    DECLARE completadas INT DEFAULT 0;
    DECLARE porcentaje DECIMAL(5,2);

    SELECT COUNT(*) INTO total
    FROM actividad a
    JOIN materias m ON a.id_materia = m.id_materias
    WHERE m.id_usuario = idUser
    AND a.id_periodo = idPeriodo;

    SELECT COUNT(*) INTO completadas
    FROM actividad a
    JOIN materias m ON a.id_materia = m.id_materias
    WHERE m.id_usuario = idUser
    AND a.id_periodo = idPeriodo
    AND a.id_estado = 2;

    IF total = 0 THEN
        SET porcentaje = 0;
    ELSE
        SET porcentaje = (completadas / total) * 100;
    END IF;

    INSERT INTO indicador_academico
    (id_usuario, porcentaje_cumplimiento, fecha_calculo, id_periodo)
    VALUES (idUser, porcentaje, CURDATE(), idPeriodo);
END//

DELIMITER ;

CREATE VIEW vista_actividades_estudiantes AS
SELECT
    u.id_usuario AS id_usuario,
    u.nombre AS estudiante,
    a.id_actividad AS id_actividad,
    a.nombre AS actividad,
    a.fecha_entrega AS fecha_entrega,
    calcular_estado_actividad(a.fecha_entrega, a.id_estado) AS estado,
    p.nombre AS periodo
FROM actividad a
JOIN materias m ON a.id_materia = m.id_materias
JOIN usuario u ON m.id_usuario = u.id_usuario
JOIN periodo p ON a.id_periodo = p.id_periodo;
