<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/db.php';
include '../includes/header.php';

// Lógica para manejar el CRUD de campeonatos
$mensaje = '';

// Crear o Actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_campeonato'])) {
    $id_campeonato = $_POST['id_campeonato'] ?? null;
    $nombre_campeonato = $_POST['nombre_campeonato'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    if ($id_campeonato) {
        // Actualizar
        $stmt = $pdo->prepare("UPDATE campeonatos SET nombre_campeonato = ?, descripcion = ?, fecha_inicio = ?, fecha_fin = ? WHERE id_campeonato = ?");
        $stmt->execute([$nombre_campeonato, $descripcion, $fecha_inicio, $fecha_fin, $id_campeonato]);
        $mensaje = "Campeonato actualizado con éxito.";
    } else {
        // Crear
        $stmt = $pdo->prepare("INSERT INTO campeonatos (nombre_campeonato, descripcion, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre_campeonato, $descripcion, $fecha_inicio, $fecha_fin]);
        $mensaje = "Campeonato creado con éxito.";
    }
}

// Eliminar
if (isset($_GET['eliminar'])) {
    $id_campeonato = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM campeonatos WHERE id_campeonato = ?");
    $stmt->execute([$id_campeonato]);
    $mensaje = "Campeonato eliminado con éxito.";
}

// Obtener campeonato para editar
$campeonato_a_editar = null;
if (isset($_GET['editar'])) {
    $id_campeonato = $_GET['editar'];
    $stmt = $pdo->prepare("SELECT * FROM campeonatos WHERE id_campeonato = ?");
    $stmt->execute([$id_campeonato]);
    $campeonato_a_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todos los campeonatos
$campeonatos = $pdo->query("SELECT * FROM campeonatos ORDER BY fecha_inicio DESC")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container mt-5">
    <h2>Gestión de Campeonatos</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <?php echo $campeonato_a_editar ? 'Editar Campeonato' : 'Nuevo Campeonato'; ?>
        </div>
        <div class="card-body">
            <form action="campeonatos.php" method="post">
                <input type="hidden" name="id_campeonato" value="<?php echo $campeonato_a_editar['id_campeonato'] ?? ''; ?>">
                <div class="form-group">
                    <label for="nombre_campeonato">Nombre del Campeonato</label>
                    <input type="text" class="form-control" id="nombre_campeonato" name="nombre_campeonato" value="<?php echo htmlspecialchars($campeonato_a_editar['nombre_campeonato'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion"><?php echo htmlspecialchars($campeonato_a_editar['descripcion'] ?? ''); ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="fecha_inicio">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo $campeonato_a_editar['fecha_inicio'] ?? ''; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="fecha_fin">Fecha de Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo $campeonato_a_editar['fecha_fin'] ?? ''; ?>">
                    </div>
                </div>
                <button type="submit" name="guardar_campeonato" class="btn btn-primary">Guardar</button>
                <?php if ($campeonato_a_editar): ?>
                    <a href="campeonatos.php" class="btn btn-secondary">Cancelar Edición</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Lista de Campeonatos
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campeonatos as $campeonato): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($campeonato['nombre_campeonato']); ?></td>
                            <td><?php echo htmlspecialchars($campeonato['descripcion']); ?></td>
                            <td><?php echo $campeonato['fecha_inicio']; ?></td>
                            <td><?php echo $campeonato['fecha_fin']; ?></td>
                            <td>
                                <a href="campeonatos.php?editar=<?php echo $campeonato['id_campeonato']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="campeonatos.php?eliminar=<?php echo $campeonato['id_campeonato']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este campeonato?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
