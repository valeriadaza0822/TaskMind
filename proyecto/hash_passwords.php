<?php
include("config/conexion.php");

$sql = "SELECT id_usuario, contrasena FROM usuario";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $id = $row['id_usuario'];
    $plain = $row['contrasena'];
    if (!password_get_info($plain)['algo']) { // if not hashed
        $hashed = password_hash($plain, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuario SET contrasena = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $hashed, $id);
        $stmt->execute();
        echo "Updated user $id\n";
    }
}

echo "Hashing completed.\n";
?>