<?php
require '../../config/db.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT * FROM anuncios ORDER BY fecha_creacion DESC LIMIT 5");
$anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($anuncios);
?>