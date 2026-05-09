<?php
$conn = new mysqli('localhost','root','','taskmind');
$conn->set_charset('utf8mb4');
$row = $conn->query('SELECT nombre_materia FROM materias LIMIT 1')->fetch_assoc();
$original = $row['nombre_materia'];
function show($label, $text) {
    echo $label . ': ' . $text . "\n";
}
show('ORIGINAL', $original);
show('UTF8_ENCODE', utf8_encode($original));
show('UTF8_DECODE', utf8_decode($original));
show('MB ISO-8859-1 -> UTF8', mb_convert_encoding($original, 'UTF-8', 'ISO-8859-1'));
show('MB CP1252 -> UTF8', mb_convert_encoding($original, 'UTF-8', 'CP1252'));
show('MB UTF8 -> ISO-8859-1', mb_convert_encoding($original, 'ISO-8859-1', 'UTF-8'));
show('MB UTF8 -> UTF8', mb_convert_encoding($original, 'UTF-8', 'UTF-8'));
show('MB CP850 -> UTF8', mb_convert_encoding($original, 'UTF-8', 'CP850'));
show('BINARY->UTF8', mb_convert_encoding($original, 'UTF-8', 'BINARY'));
show('CP1252 bytes->UTF8', mb_convert_encoding($original, 'UTF-8', 'CP1252'));
show('UTF8 bytes->Latin1', mb_convert_encoding($original, 'ISO-8859-1', 'UTF-8'));
?>