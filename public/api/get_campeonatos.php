<?php
require_once '../../config/db.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM campeonatos ORDER BY fecha_inicio DESC");
    $campeonatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($campeonatos);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener los campeonatos: ' . $e->getMessage()]);
}
?>