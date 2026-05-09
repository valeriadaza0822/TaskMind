-- INSERTS DE PRUEBA PARA TASKMIND
-- Datos de ejemplo para todas las tablas

-- TABLA: tipo_periodo
INSERT INTO tipo_periodo (id_tipo_periodo, nombre) VALUES
(1, 'Semestral'),
(2, 'Trimestral'),
(3, 'Cuatrimestral'),
(4, 'Anual');

-- TABLA: institucion
INSERT INTO institucion (id_institucion, nombre, id_tipo_periodo) VALUES
(1, 'Universidad Nacional de Colombia', 1),
(2, 'Pontificia Universidad Javeriana', 1),
(3, 'Universidad de los Andes', 1),
(4, 'Colegio Mayor de San Bartolomé', 2),
(5, 'Gimnasio Los Andes', 2);

-- TABLA: sede
INSERT INTO sede (id_sede, nombre, direccion, id_institucion) VALUES
(1, 'Sede Bogotá', 'Carrera 45 No. 26-85, Bogotá', 1),
(2, 'Sede Medellín', 'Calle 50 No. 52-42, Medellín', 1),
(3, 'Sede Principal', 'Avenida Paseo Colón 73-60, Bogotá', 2),
(4, 'Campus Siberia', 'Carrera 1 Este No. 19a-40, Bogotá', 3),
(5, 'Sede Centro', 'Carrera 7 No. 24-89, Bogotá', 4);

-- TABLA: estado
INSERT INTO estado (id_estado, nombre) VALUES
(1, 'Pendiente'),
(2, 'Completada'),
(3, 'Vencida');

-- TABLA: estado_alerta
INSERT INTO estado_alerta (id_estado_alerta, nombre) VALUES
(1, 'Sin leer'),
(2, 'Leída');

-- TABLA: estado_general
INSERT INTO estado_general (id_estado, nombre) VALUES
(1, 'Activo'),
(2, 'Inactivo'),
(3, 'Suspendido');

-- TABLA: nivel_riesgo
INSERT INTO nivel_riesgo (id_nivel_riesgo, nombre) VALUES
(1, 'Bajo'),
(2, 'Medio'),
(3, 'Alto'),
(4, 'Crítico');

-- TABLA: prioridad
INSERT INTO prioridad (id_prioridad, nombre) VALUES
(1, 'Baja'),
(2, 'Media'),
(3, 'Alta'),
(4, 'Urgente');

-- TABLA: usuario
INSERT INTO usuario (id_usuario, nombre, correo, contrasena, fecha_registro, id_institucion, id_estado, telefono, documento, institucion_texto, programa_academico, semestre_actual, jornada) VALUES
(1, 'Tatiana Lopez', 'tatianalopez@gmail.com', '123456', '2025-09-15', 1, 1, '3001234567', '1023900243', 'Universidad Nacional de Colombia', 'Ingeniería de Sistemas', 5, 'Mañana'),
(2, 'Juan Pérez', 'juan.perez@gmail.com', '654321', '2025-10-01', 2, 1, '3102345678', '1098765432', 'Pontificia Universidad Javeriana', 'Administración de Empresas', 3, 'Tarde'),
(3, 'María García', 'maria.garcia@gmail.com', '111111', '2025-10-10', 3, 1, '3203456789', '1087654321', 'Universidad de los Andes', 'Derecho', 4, 'Noche'),
(4, 'Carlos Rodríguez', 'carlos.rod@gmail.com', '222222', '2025-10-15', 4, 1, '3304567890', '1076543210', 'Colegio Mayor de San Bartolomé', 'Contabilidad', 2, 'Mañana'),
(5, 'Ana Martínez', 'ana.martinez@gmail.com', '333333', '2025-10-20', 5, 1, '3405678901', '1065432109', 'Gimnasio Los Andes', 'Psicología', 6, 'Virtual');

-- TABLA: periodo
INSERT INTO periodo (id_periodo, nombre, fecha_inicio, fecha_fin, id_institucion) VALUES
(1, '2025-I', '2025-01-15', '2025-06-30', 1),
(2, '2025-II', '2025-07-01', '2025-12-20', 1),
(3, 'Trimestre 1', '2025-01-01', '2025-03-31', 4),
(4, 'Trimestre 2', '2025-04-01', '2025-06-30', 4),
(5, '2025-A', '2025-08-01', '2025-12-31', 2);

-- TABLA: materias
INSERT INTO materias (id_materias, nombre_materia, descripcion, semestre, id_usuario) VALUES
(1, 'Matemáticas I', 'Cálculo diferencial e integral', 1, 1),
(2, 'Programación Básica', 'Introducción a la programación con Python', 1, 1),
(3, 'Física General', 'Mecánica clásica', 1, 1),
(4, 'Bases de Datos', 'Diseño y modelado de bases de datos', 3, 1),
(5, 'Inglés Avanzado', 'Business English', 2, 2),
(6, 'Contabilidad General', 'Fundamentos contables', 1, 4),
(7, 'Derecho Constitucional', 'Estudio de la constitución nacional', 1, 3),
(8, 'Desarrollo Web', 'HTML, CSS, JavaScript', 3, 1),
(9, 'Psicología del Aprendizaje', 'Teorías de aprendizaje', 1, 5),
(10, 'Administración Financiera', 'Gestión financiera empresarial', 4, 2);

-- TABLA: actividad (TAREAS/ACTIVIDADES)
INSERT INTO actividad (id_actividad, nombre, descripcion, fecha_entrega, tipo_periodo_texto, periodo_texto, fecha_completada, id_materia, id_estado, id_prioridad, id_periodo) VALUES
(1, 'Taller Ecuaciones Diferenciales', 'Resolver 20 ejercicios de ecuaciones diferenciales', '2025-05-20', 'Semestral', '2025-I', NULL, 1, 1, 3, 1),
(2, 'Proyecto Python - Sistema de Notas', 'Crear un programa que gestione calificaciones', '2025-05-25', 'Semestral', '2025-I', NULL, 2, 1, 4, 1),
(3, 'Laboratorio de Física - Péndulo', 'Experimentar con movimiento pendular', '2025-05-15', 'Semestral', '2025-I', '2025-05-14 14:30:00', 3, 2, 2, 1),
(4, 'Diseño de Base de Datos - Proyecto Final', 'Modelar BD para sistema de biblioteca', '2025-06-10', 'Semestral', '2025-I', NULL, 4, 1, 3, 1),
(5, 'Presentación Oral - Inglés', 'Presentar proyecto sobre cultura inglesa', '2025-05-30', 'Semestral', '2025-I', NULL, 5, 1, 2, 1),
(6, 'Ensayo Jurídico', 'Análisis crítico de sentencia constitucional', '2025-06-05', 'Semestral', '2025-I', NULL, 7, 1, 3, 1),
(7, 'Parcial Matemáticas I', 'Examen teórico-práctico', '2025-05-28', 'Semestral', '2025-I', NULL, 1, 3, 4, 1),
(8, 'Página Web Personal', 'Crear portafolio con HTML y CSS', '2025-06-01', 'Semestral', '2025-I', '2025-05-31 10:15:00', 8, 2, 2, 1),
(9, 'Reseña Teórica Psicología', 'Resumen de teoría de Piaget', '2025-05-25', 'Trimestral', 'Trimestre 1', NULL, 9, 1, 2, 3),
(10, 'Análisis Financiero', 'Estado de resultados empresa XYZ', '2025-06-15', 'Semestral', '2025-I', NULL, 10, 1, 3, 1);

-- TABLA: contacto
INSERT INTO contacto (id_contacto, nombre, correo, tipo_mensaje, mensaje, fecha_envio) VALUES
(1, 'Pedro González', 'pedro.gonzalez@email.com', 'Sugerencia', 'La aplicación es muy útil, sugerencia: agregar notificaciones por correo', '2025-05-10'),
(2, 'Laura Rodríguez', 'laura.rod@email.com', 'Problema', 'Problema: No puedo ver mis tareas completadas en el historial', '2025-05-12'),
(3, 'Roberto Silva', 'r.silva@email.com', 'Opinión', 'Excelente herramienta para organizar mi semestre académico', '2025-05-15'),
(4, 'Sofia Mendez', 'sofia.m@email.com', 'Sugerencia', 'Agregar opción de exportar calendario a PDF', '2025-05-18'),
(5, 'Miguel Torres', 'miguel.torres@email.com', 'Problema', 'Error al guardar cambios en el perfil', '2025-05-20');

-- TABLA: alerta
INSERT INTO alerta (id_alertas, mensaje, fecha_alerta, id_estado_alerta, id_actividad) VALUES
(1, 'Tarea próxima a vencer: Taller Ecuaciones Diferenciales vence el 2025-05-20', '2025-05-19 09:00:00', 1, 1),
(2, 'Actividad vencida: Parcial Matemáticas I', '2025-05-28 10:00:00', 1, 7),
(3, 'Tarea próxima a vencer: Presentación Oral - Inglés', '2025-05-28 14:00:00', 1, 5),
(4, 'Recordatorio: Diseño de Base de Datos vence pronto', '2025-06-08 08:30:00', 2, 4),
(5, 'Felicidades: completaste Laboratorio de Física', '2025-05-14 15:00:00', 2, 3);

-- TABLA: historial_actividad
INSERT INTO historial_actividad (id_historial, id_actividad, accion, fecha) VALUES
(1, 3, 'Creada', '2025-05-01 10:00:00'),
(2, 3, 'Actualizada', '2025-05-10 14:30:00'),
(3, 3, 'Completada', '2025-05-14 14:30:00'),
(4, 8, 'Creada', '2025-05-05 09:00:00'),
(5, 8, 'Completada', '2025-05-31 10:15:00'),
(6, 1, 'Creada', '2025-04-20 11:00:00'),
(7, 2, 'Creada', '2025-04-25 08:30:00'),
(8, 7, 'Creada', '2025-04-15 13:45:00'),
(9, 7, 'Vencida', '2025-05-28 23:59:00'),
(10, 4, 'Creada', '2025-04-28 10:00:00');

-- TABLA: historico_usuario
INSERT INTO historico_usuario (id_historico, id_usuario, tabla_afectada, accion, descripcion, fecha) VALUES
(1, 1, 'usuario', 'INSERT', 'Nuevo usuario registrado', '2025-09-15 14:20:00'),
(2, 1, 'usuario', 'UPDATE', 'Actualización de perfil - cambio de semestre', '2025-10-01 10:30:00'),
(3, 1, 'materias', 'INSERT', 'Se agregó materia: Bases de Datos', '2025-04-20 11:00:00'),
(4, 1, 'actividad', 'INSERT', 'Se creó tarea: Taller Ecuaciones Diferenciales', '2025-04-20 11:05:00'),
(5, 2, 'usuario', 'INSERT', 'Nuevo usuario registrado', '2025-10-01 15:45:00'),
(6, 3, 'usuario', 'INSERT', 'Nuevo usuario registrado', '2025-10-10 09:30:00'),
(7, 1, 'actividad', 'UPDATE', 'Tarea Laboratorio de Física marcada como completada', '2025-05-14 14:30:00'),
(8, 4, 'usuario', 'INSERT', 'Nuevo usuario registrado', '2025-10-15 11:00:00'),
(9, 5, 'usuario', 'INSERT', 'Nuevo usuario registrado', '2025-10-20 16:20:00'),
(10, 1, 'actividad', 'UPDATE', 'Actualización de descripción - Proyecto Python', '2025-04-30 13:00:00');

-- TABLA: indicador_academico
INSERT INTO indicador_academico (id_indicador_academico, porcentaje_cumplimiento, fecha_calculo, id_usuario, id_periodo, id_nivel) VALUES
(1, 85.50, '2025-05-15', 1, 1, 1),
(2, 78.00, '2025-05-15', 2, 5, 2),
(3, 92.30, '2025-05-15', 3, 1, 1),
(4, 65.75, '2025-05-15', 4, 3, 3),
(5, 88.20, '2025-05-15', 5, 3, 1),
(6, 80.00, '2025-05-20', 1, 1, 1),
(7, 76.50, '2025-05-20', 2, 5, 2),
(8, 90.00, '2025-05-20', 3, 1, 1),
(9, 70.25, '2025-05-20', 4, 3, 2),
(10, 89.00, '2025-05-20', 5, 3, 1);
