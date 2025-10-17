<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/db.php';
include '../includes/header.php';

$mensaje = '';

// Crear o Actualizar Equipo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_equipo'])) {
    $id_equipo = $_POST['id_equipo'] ?? null;
    $nombre_equipo = $_POST['nombre_equipo'];
    $descripcion = $_POST['descripcion'];
    $logo_equipo = $_POST['logo_equipo_actual'] ?? '';

    // Manejo de la subida del logo
    if (isset($_FILES['logo_equipo']) && $_FILES['logo_equipo']['error'] == 0) {
        $target_dir = "../uploads/logos/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["logo_equipo"]["name"]);
        if (move_uploaded_file($_FILES["logo_equipo"]["tmp_name"], $target_file)) {
            $logo_equipo = "uploads/logos/" . basename($_FILES["logo_equipo"]["name"]);
        } else {
            $mensaje = "Error al subir el logo.";
        }
    }

    if (empty($mensaje)) {
        if ($id_equipo) {
            // Actualizar
            $stmt = $pdo->prepare("UPDATE equipos SET nombre_equipo = ?, descripcion = ?, logo_equipo = ? WHERE id_equipo = ?");
            $stmt->execute([$nombre_equipo, $descripcion, $logo_equipo, $id_equipo]);
            $mensaje = "Equipo actualizado con éxito.";
        } else {
            // Crear
            $stmt = $pdo->prepare("INSERT INTO equipos (nombre_equipo, descripcion, logo_equipo) VALUES (?, ?, ?)");
            $stmt->execute([$nombre_equipo, $descripcion, $logo_equipo]);
            $mensaje = "Equipo creado con éxito.";
        }
    }
}

// Eliminar Equipo
if (isset($_GET['eliminar'])) {
    $id_equipo = $_GET['eliminar'];
    // Opcional: eliminar el archivo del logo del servidor
    $stmt = $pdo->prepare("SELECT logo_equipo FROM equipos WHERE id_equipo = ?");
    $stmt->execute([$id_equipo]);
    $equipo = $stmt->fetch();
    if ($equipo && !empty($equipo['logo_equipo']) && file_exists('../' . $equipo['logo_equipo'])) {
        unlink('../' . $equipo['logo_equipo']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM equipos WHERE id_equipo = ?");
    $stmt->execute([$id_equipo]);
    $mensaje = "Equipo eliminado con éxito.";
}

// Obtener equipo para editar
$equipo_a_editar = null;
if (isset($_GET['editar'])) {
    $id_equipo = $_GET['editar'];
    $stmt = $pdo->prepare("SELECT * FROM equipos WHERE id_equipo = ?");
    $stmt->execute([$id_equipo]);
    $equipo_a_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todos los equipos
$equipos = $pdo->query("SELECT * FROM equipos ORDER BY nombre_equipo")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Gestión de Equipos</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><?php echo $equipo_a_editar ? 'Editar Equipo' : 'Nuevo Equipo'; ?></div>
        <div class="card-body">
            <form action="equipos.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_equipo" value="<?php echo $equipo_a_editar['id_equipo'] ?? ''; ?>">
                <input type="hidden" name="logo_equipo_actual" value="<?php echo $equipo_a_editar['logo_equipo'] ?? ''; ?>">
                <div class="form-group">
                    <label for="nombre_equipo">Nombre del Equipo</label>
                    <input type="text" class="form-control" id="nombre_equipo" name="nombre_equipo" value="<?php echo htmlspecialchars($equipo_a_editar['nombre_equipo'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion"><?php echo htmlspecialchars($equipo_a_editar['descripcion'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="logo_equipo">Logo del Equipo</label>
                    <input type="file" class="form-control-file" id="logo_equipo" name="logo_equipo">
                    <?php if (isset($equipo_a_editar['logo_equipo']) && !empty($equipo_a_editar['logo_equipo'])): ?>
                        <img src="../<?php echo htmlspecialchars($equipo_a_editar['logo_equipo']); ?>" alt="Logo" width="100" class="mt-2">
                    <?php endif; ?>
                </div>
                <button type="submit" name="guardar_equipo" class="btn btn-primary">Guardar</button>
                <?php if ($equipo_a_editar): ?>
                    <a href="equipos.php" class="btn btn-secondary">Cancelar Edición</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Lista de Equipos</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipos as $equipo): ?>
                        <tr>
                            <td>
                                <?php if (!empty($equipo['logo_equipo'])): ?>
                                    <img src="../<?php echo htmlspecialchars($equipo['logo_equipo']); ?>" alt="Logo" width="50">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></td>
                            <td><?php echo htmlspecialchars($equipo['descripcion']); ?></td>
                            <td>
                                <a href="equipos.php?editar=<?php echo $equipo['id_equipo']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="equipos.php?eliminar=<?php echo $equipo['id_equipo']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este equipo?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
