<?php
require '../../config/db.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT * FROM banners ORDER BY fecha_creacion DESC");
$banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($banners);
?>