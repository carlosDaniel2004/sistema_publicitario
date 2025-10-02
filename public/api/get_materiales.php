<?php
require '../../config/db.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT * FROM materiales ORDER BY fecha_creacion DESC");
$materiales = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($materiales);
?>