<?php
require_once '../../config/db.php';

header('Content-Type: application/json');

try {
    $query = "
        SELECT 
            p.id_partido,
            p.fecha_partido,
            p.lugar,
            p.resultado_local,
            p.resultado_visitante,
            p.estado,
            c.nombre_campeonato,
            el.nombre_equipo as equipo_local,
            ev.nombre_equipo as equipo_visitante
        FROM partidos p
        JOIN campeonatos c ON p.id_campeonato = c.id_campeonato
        JOIN equipos el ON p.id_equipo_local = el.id_equipo
        JOIN equipos ev ON p.id_equipo_visitante = ev.id_equipo
        ORDER BY p.fecha_partido DESC
    ";
    $stmt = $pdo->query($query);
    $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($partidos);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener los partidos: ' . $e->getMessage()]);
}
?>