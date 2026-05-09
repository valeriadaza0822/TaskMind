<?php
$conn = new mysqli('localhost','root','','taskmind');
$conn->set_charset('utf8mb4');
if ($conn->connect_error) { echo $conn->connect_error; exit; }
$row = $conn->query('SELECT nombre_materia FROM materias LIMIT 1')->fetch_assoc();
$original = $row['nombre_materia'];
echo "ORIGINAL: $original\n";
echo "BIN HEX: " . bin2hex($original) . "\n";
$latin = mb_convert_encoding($original, 'UTF-8', 'ISO-8859-1');
echo "LATIN->UTF8: $latin\n";
$utf8 = mb_convert_encoding($original, 'UTF-8', 'UTF-8');
echo "UTF8->UTF8: $utf8\n";
$cp1252 = mb_convert_encoding($original, 'UTF-8', 'CP1252');
echo "CP1252->UTF8: $cp1252\n";
$latin1 = mb_convert_encoding($original, 'ISO-8859-1', 'UTF-8');
echo "UTF8->LATIN1: $latin1\n";
$correct = mb_convert_encoding($original, 'UTF-8', 'UTF-8');
echo "correct1: $correct\n";
?>