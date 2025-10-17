<?php
require_once '../../config/db.php';

header('Content-Type: application/json');

try {
    $query = "
        SELECT j.id_jugador, j.nombre_jugador, j.posicion, j.descripcion, e.nombre_equipo 
        FROM jugadores j
        LEFT JOIN equipos e ON j.id_equipo = e.id_equipo
        ORDER BY j.nombre_jugador
    ";
    $stmt = $pdo->query($query);
    $jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($jugadores);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener los jugadores: ' . $e->getMessage()]);
}
?>