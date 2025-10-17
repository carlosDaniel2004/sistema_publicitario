<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/db.php';
include '../includes/header.php';

$mensaje = '';

// Obtener campeonatos y equipos para los dropdowns
$campeonatos = $pdo->query("SELECT id_campeonato, nombre_campeonato FROM campeonatos ORDER BY nombre_campeonato")->fetchAll(PDO::FETCH_ASSOC);
$equipos = $pdo->query("SELECT id_equipo, nombre_equipo FROM equipos ORDER BY nombre_equipo")->fetchAll(PDO::FETCH_ASSOC);

// Crear o Actualizar Partido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_partido'])) {
    $id_partido = $_POST['id_partido'] ?? null;
    $id_campeonato = $_POST['id_campeonato'];
    $id_equipo_local = $_POST['id_equipo_local'];
    $id_equipo_visitante = $_POST['id_equipo_visitante'];
    $fecha_partido = $_POST['fecha_partido'];
    $lugar = $_POST['lugar'];
    $resultado_local = $_POST['resultado_local'] ?: null;
    $resultado_visitante = $_POST['resultado_visitante'] ?: null;
    $estado = $_POST['estado'];

    if ($id_partido) {
        // Actualizar
        $stmt = $pdo->prepare("UPDATE partidos SET id_campeonato = ?, id_equipo_local = ?, id_equipo_visitante = ?, fecha_partido = ?, lugar = ?, resultado_local = ?, resultado_visitante = ?, estado = ? WHERE id_partido = ?");
        $stmt->execute([$id_campeonato, $id_equipo_local, $id_equipo_visitante, $fecha_partido, $lugar, $resultado_local, $resultado_visitante, $estado, $id_partido]);
        $mensaje = "Partido actualizado con éxito.";
    } else {
        // Crear
        $stmt = $pdo->prepare("INSERT INTO partidos (id_campeonato, id_equipo_local, id_equipo_visitante, fecha_partido, lugar, resultado_local, resultado_visitante, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_campeonato, $id_equipo_local, $id_equipo_visitante, $fecha_partido, $lugar, $resultado_local, $resultado_visitante, $estado]);
        $mensaje = "Partido creado con éxito.";
    }
}

// Eliminar Partido
if (isset($_GET['eliminar'])) {
    $id_partido = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM partidos WHERE id_partido = ?");
    $stmt->execute([$id_partido]);
    $mensaje = "Partido eliminado con éxito.";
}

// Obtener partido para editar
$partido_a_editar = null;
if (isset($_GET['editar'])) {
    $id_partido = $_GET['editar'];
    $stmt = $pdo->prepare("SELECT * FROM partidos WHERE id_partido = ?");
    $stmt->execute([$id_partido]);
    $partido_a_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todos los partidos
$partidos = $pdo->query("
    SELECT p.*, c.nombre_campeonato, el.nombre_equipo as local, ev.nombre_equipo as visitante
    FROM partidos p
    JOIN campeonatos c ON p.id_campeonato = c.id_campeonato
    JOIN equipos el ON p.id_equipo_local = el.id_equipo
    JOIN equipos ev ON p.id_equipo_visitante = ev.id_equipo
    ORDER BY p.fecha_partido DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container mt-5">
    <h2>Gestión de Partidos</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><?php echo $partido_a_editar ? 'Editar Partido' : 'Nuevo Partido'; ?></div>
        <div class="card-body">
            <form action="partidos.php" method="post">
                <input type="hidden" name="id_partido" value="<?php echo $partido_a_editar['id_partido'] ?? ''; ?>">
                
                <div class="form-group">
                    <label for="id_campeonato">Campeonato</label>
                    <select class="form-control" id="id_campeonato" name="id_campeonato" required>
                        <?php foreach ($campeonatos as $c): ?>
                            <option value="<?php echo $c['id_campeonato']; ?>" <?php echo (isset($partido_a_editar['id_campeonato']) && $partido_a_editar['id_campeonato'] == $c['id_campeonato']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['nombre_campeonato']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="id_equipo_local">Equipo Local</label>
                        <select class="form-control" id="id_equipo_local" name="id_equipo_local" required>
                            <?php foreach ($equipos as $e): ?>
                                <option value="<?php echo $e['id_equipo']; ?>" <?php echo (isset($partido_a_editar['id_equipo_local']) && $partido_a_editar['id_equipo_local'] == $e['id_equipo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($e['nombre_equipo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="id_equipo_visitante">Equipo Visitante</label>
                        <select class="form-control" id="id_equipo_visitante" name="id_equipo_visitante" required>
                            <?php foreach ($equipos as $e): ?>
                                <option value="<?php echo $e['id_equipo']; ?>" <?php echo (isset($partido_a_editar['id_equipo_visitante']) && $partido_a_editar['id_equipo_visitante'] == $e['id_equipo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($e['nombre_equipo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="fecha_partido">Fecha y Hora</label>
                        <input type="datetime-local" class="form-control" id="fecha_partido" name="fecha_partido" value="<?php echo isset($partido_a_editar['fecha_partido']) ? date('Y-m-d\TH:i', strtotime($partido_a_editar['fecha_partido'])) : ''; ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="lugar">Lugar</label>
                        <input type="text" class="form-control" id="lugar" name="lugar" value="<?php echo htmlspecialchars($partido_a_editar['lugar'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="resultado_local">Resultado Local</label>
                        <input type="number" class="form-control" id="resultado_local" name="resultado_local" value="<?php echo htmlspecialchars($partido_a_editar['resultado_local'] ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="resultado_visitante">Resultado Visitante</label>
                        <input type="number" class="form-control" id="resultado_visitante" name="resultado_visitante" value="<?php echo htmlspecialchars($partido_a_editar['resultado_visitante'] ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="estado">
                            <option value="pendiente" <?php echo (isset($partido_a_editar['estado']) && $partido_a_editar['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="en_juego" <?php echo (isset($partido_a_editar['estado']) && $partido_a_editar['estado'] == 'en_juego') ? 'selected' : ''; ?>>En Juego</option>
                            <option value="finalizado" <?php echo (isset($partido_a_editar['estado']) && $partido_a_editar['estado'] == 'finalizado') ? 'selected' : ''; ?>>Finalizado</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="guardar_partido" class="btn btn-primary">Guardar</button>
                <?php if ($partido_a_editar): ?>
                    <a href="partidos.php" class="btn btn-secondary">Cancelar Edición</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Lista de Partidos</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Campeonato</th>
                        <th>Fecha</th>
                        <th>Local vs Visitante</th>
                        <th>Resultado</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($partidos as $partido): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($partido['nombre_campeonato']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($partido['fecha_partido'])); ?></td>
                            <td><?php echo htmlspecialchars($partido['local']); ?> vs <?php echo htmlspecialchars($partido['visitante']); ?></td>
                            <td><?php echo $partido['resultado_local']; ?> - <?php echo $partido['resultado_visitante']; ?></td>
                            <td><?php echo htmlspecialchars($partido['estado']); ?></td>
                            <td>
                                <a href="partidos.php?editar=<?php echo $partido['id_partido']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="partidos.php?eliminar=<?php echo $partido['id_partido']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este partido?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
