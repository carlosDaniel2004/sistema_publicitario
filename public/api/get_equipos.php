<?php
require_once '../../config/db.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM equipos ORDER BY nombre_equipo");
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($equipos);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener los equipos: ' . $e->getMessage()]);
}
?>