<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/db.php';
include '../includes/header.php';

$mensaje = '';

// Obtener equipos para el dropdown
$equipos = $pdo->query("SELECT id_equipo, nombre_equipo FROM equipos ORDER BY nombre_equipo")->fetchAll(PDO::FETCH_ASSOC);

// Crear o Actualizar Jugador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_jugador'])) {
    $id_jugador = $_POST['id_jugador'] ?? null;
    $nombre_jugador = $_POST['nombre_jugador'];
    $posicion = $_POST['posicion'];
    $id_equipo = $_POST['id_equipo'];
    $descripcion = $_POST['descripcion'];
    $foto_jugador = $_POST['foto_actual'] ?? '';

    // Manejo de la subida de la foto
    if (isset($_FILES['foto_jugador']) && $_FILES['foto_jugador']['error'] == 0) {
        $target_dir = "../uploads/jugadores/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . uniqid() . '_' . basename($_FILES["foto_jugador"]["name"]);
        if (move_uploaded_file($_FILES["foto_jugador"]["tmp_name"], $target_file)) {
            $foto_jugador = "uploads/jugadores/" . basename($target_file);
        } else {
            $mensaje = "Error al subir la foto.";
        }
    }

    if (empty($mensaje)) {
        if ($id_jugador) {
            // Actualizar
            $stmt = $pdo->prepare("UPDATE jugadores SET nombre_jugador = ?, posicion = ?, id_equipo = ?, descripcion = ?, foto_jugador = ? WHERE id_jugador = ?");
            $stmt->execute([$nombre_jugador, $posicion, $id_equipo, $descripcion, $foto_jugador, $id_jugador]);
            $mensaje = "Jugador actualizado con éxito.";
        } else {
            // Crear
            $stmt = $pdo->prepare("INSERT INTO jugadores (nombre_jugador, posicion, id_equipo, descripcion, foto_jugador) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre_jugador, $posicion, $id_equipo, $descripcion, $foto_jugador]);
            $mensaje = "Jugador creado con éxito.";
        }
    }
}

// Eliminar Jugador
if (isset($_GET['eliminar'])) {
    $id_jugador = $_GET['eliminar'];
    // Opcional: eliminar el archivo de la foto del servidor
    $stmt = $pdo->prepare("SELECT foto_jugador FROM jugadores WHERE id_jugador = ?");
    $stmt->execute([$id_jugador]);
    $jugador = $stmt->fetch();
    if ($jugador && !empty($jugador['foto_jugador']) && file_exists('../' . $jugador['foto_jugador'])) {
        unlink('../' . $jugador['foto_jugador']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM jugadores WHERE id_jugador = ?");
    $stmt->execute([$id_jugador]);
    $mensaje = "Jugador eliminado con éxito.";
}

// Obtener jugador para editar
$jugador_a_editar = null;
if (isset($_GET['editar'])) {
    $id_jugador = $_GET['editar'];
    $stmt = $pdo->prepare("SELECT * FROM jugadores WHERE id_jugador = ?");
    $stmt->execute([$id_jugador]);
    $jugador_a_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todos los jugadores con el nombre de su equipo
$jugadores = $pdo->query("
    SELECT j.*, e.nombre_equipo 
    FROM jugadores j
    LEFT JOIN equipos e ON j.id_equipo = e.id_equipo
    ORDER BY j.nombre_jugador
")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container mt-5">
    <h2>Gestión de Jugadores</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><?php echo $jugador_a_editar ? 'Editar Jugador' : 'Nuevo Jugador'; ?></div>
        <div class="card-body">
            <form action="jugadores.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_jugador" value="<?php echo $jugador_a_editar['id_jugador'] ?? ''; ?>">
                <input type="hidden" name="foto_actual" value="<?php echo $jugador_a_editar['foto_jugador'] ?? ''; ?>">
                <div class="form-group mb-3">
                    <label for="nombre_jugador">Nombre del Jugador</label>
                    <input type="text" class="form-control" id="nombre_jugador" name="nombre_jugador" value="<?php echo htmlspecialchars($jugador_a_editar['nombre_jugador'] ?? ''); ?>" required>
                </div>
                <div class="row mb-3">
                    <div class="form-group col-md-6">
                        <label for="posicion">Posición</label>
                        <input type="text" class="form-control" id="posicion" name="posicion" value="<?php echo htmlspecialchars($jugador_a_editar['posicion'] ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="id_equipo">Equipo</label>
                        <select class="form-control" id="id_equipo" name="id_equipo">
                            <option value="">Sin equipo</option>
                            <?php foreach ($equipos as $equipo): ?>
                                <option value="<?php echo $equipo['id_equipo']; ?>" <?php echo (isset($jugador_a_editar['id_equipo']) && $jugador_a_editar['id_equipo'] == $equipo['id_equipo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($equipo['nombre_equipo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="foto_jugador">Foto del Jugador</label>
                    <input type="file" class="form-control" id="foto_jugador" name="foto_jugador">
                    <?php if (isset($jugador_a_editar['foto_jugador']) && !empty($jugador_a_editar['foto_jugador'])): ?>
                        <img src="../<?php echo htmlspecialchars($jugador_a_editar['foto_jugador']); ?>" alt="Foto" width="100" class="mt-2 rounded">
                    <?php endif; ?>
                </div>
                <div class="form-group mb-3">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion"><?php echo htmlspecialchars($jugador_a_editar['descripcion'] ?? ''); ?></textarea>
                </div>
                <button type="submit" name="guardar_jugador" class="btn btn-primary">Guardar</button>
                <?php if ($jugador_a_editar): ?>
                    <a href="jugadores.php" class="btn btn-secondary">Cancelar Edición</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Lista de Jugadores</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th>Posición</th>
                            <th>Equipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jugadores as $jugador): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($jugador['foto_jugador'])): ?>
                                        <img src="../<?php echo htmlspecialchars($jugador['foto_jugador']); ?>" alt="Foto" width="50" height="50" class="rounded-circle" style="object-fit: cover;">
                                    <?php else: ?>
                                        <img src="../uploads/jugadores/default.png" alt="Foto" width="50" height="50" class="rounded-circle" style="object-fit: cover;">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($jugador['nombre_jugador']); ?></td>
                                <td><?php echo htmlspecialchars($jugador['posicion']); ?></td>
                                <td><?php echo htmlspecialchars($jugador['nombre_equipo'] ?? 'Sin equipo'); ?></td>
                                <td>
                                    <a href="jugadores.php?editar=<?php echo $jugador['id_jugador']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i></a>
                                    <a href="jugadores.php?eliminar=<?php echo $jugador['id_jugador']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este jugador?');"><i class="bi bi-trash-fill"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
