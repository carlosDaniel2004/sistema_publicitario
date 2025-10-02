<?php
require '../../config/db.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT * FROM convocatorias ORDER BY fecha_creacion DESC LIMIT 5");
$convocatorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($convocatorias);
?>