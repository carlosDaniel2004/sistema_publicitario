<?php
// public/banners.php (versión actualizada)
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}
require '../config/db.php';

// Lógica para crear o actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $titulo = $_POST['titulo'];
    $enlace = !empty($_POST['enlace']) ? $_POST['enlace'] : null; // <-- CAMBIO AQUÍ
    $id_banner = $_POST['id_banner'] ?? null;
    $imagen_actual = $_POST['imagen_actual'] ?? '';
    $imagen_nombre = $imagen_actual;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/banners/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $imagen_nombre = uniqid() . '-' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . $imagen_nombre);
    }
    
    if ($id_banner) { // Actualizar
        $stmt = $pdo->prepare("UPDATE banners SET titulo = ?, imagen = ?, enlace = ? WHERE id_banner = ?");
        $stmt->execute([$titulo, $imagen_nombre, $enlace, $id_banner]); // <-- CAMBIO AQUÍ
    } else { // Crear
        $stmt = $pdo->prepare("INSERT INTO banners (titulo, imagen, enlace) VALUES (?, ?, ?)");
        $stmt->execute([$titulo, $imagen_nombre, $enlace]); // <-- CAMBIO AQUÍ
    }
    header('Location: banners.php');
    exit();
}

// Lógica para eliminar
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM banners WHERE id_banner = ?");
    $stmt->execute([$id]);
    header('Location: banners.php');
    exit();
}

// Obtener datos para editar
$banner_a_editar = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM banners WHERE id_banner = ?");
    $stmt->execute([$id]);
    $banner_a_editar = $stmt->fetch();
}

$banners = $pdo->query("SELECT * FROM banners ORDER BY fecha_creacion DESC")->fetchAll();
include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Gestión de Banners</h2>
    <div class="card mb-4">
        <div class="card-header"><?= $banner_a_editar ? 'Editar Banner' : 'Agregar Nuevo Banner' ?></div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_banner" value="<?= $banner_a_editar['id_banner'] ?? '' ?>">
                <input type="hidden" name="imagen_actual" value="<?= $banner_a_editar['imagen'] ?? '' ?>">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título del Banner</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($banner_a_editar['titulo'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="enlace" class="form-label">Enlace (Opcional)</label>
                    <input type="url" class="form-control" id="enlace" name="enlace" placeholder="https://ejemplo.com/formulario" value="<?= htmlspecialchars($banner_a_editar['enlace'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen (JPG, PNG)</label>
                    <input type="file" class="form-control" id="imagen" name="imagen" <?= !$banner_a_editar ? 'required' : '' ?>>
                    <?php if ($banner_a_editar && $banner_a_editar['imagen']): ?>
                        <img src="../uploads/banners/<?= htmlspecialchars($banner_a_editar['imagen']) ?>" width="100" class="mt-2">
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary"><?= $banner_a_editar ? 'Actualizar' : 'Guardar' ?></button>
                <?php if ($banner_a_editar): ?>
                    <a href="banners.php" class="btn btn-secondary">Cancelar Edición</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Banners Actuales</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Enlace</th> <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($banners as $banner): ?>
                    <tr>
                        <td><img src="../uploads/banners/<?= htmlspecialchars($banner['imagen']) ?>" width="150"></td>
                        <td><?= htmlspecialchars($banner['titulo']) ?></td>
                        <td>
                            <?php if ($banner['enlace']): ?>
                                <a href="<?= htmlspecialchars($banner['enlace']) ?>" target="_blank">Ver enlace</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?edit=<?= $banner['id_banner'] ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="?delete=<?= $banner['id_banner'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>