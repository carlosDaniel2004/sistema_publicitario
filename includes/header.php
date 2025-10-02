<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Panel Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="banners.php">Banners</a></li>
                <li class="nav-item"><a class="nav-link" href="anuncios.php">Anuncios</a></li>
                <li class="nav-item"><a class="nav-link" href="materiales.php">Materiales</a></li>
                <li class="nav-item"><a class="nav-link" href="convocatorias.php">Convocatorias</a></li>
                <li class="nav-item"><a class="nav-link" href="enviar_correo.php">Enviar Correos</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Cerrar Sesión (<?= htmlspecialchars($_SESSION['nombre_usuario']) ?>)</a>
                </li>
            </ul>
        </div>
    </div>
</nav>