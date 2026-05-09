<?php
$conn = new mysqli('localhost','root','','taskmind');
$conn->set_charset('utf8mb4');
if ($conn->connect_error) {
    echo "ERROR: " . $conn->connect_error;
    exit;
}
$result = $conn->query("SELECT id_actividad, nombre, HEX(nombre) AS nombre_hex FROM actividad WHERE nombre LIKE '%Laboratorio%' OR id_actividad = 3");
while ($row = $result->fetch_assoc()) {
    echo $row['id_actividad'] . "\n";
    echo $row['nombre'] . "\n";
    echo $row['nombre_hex'] . "\n";
}
?>
