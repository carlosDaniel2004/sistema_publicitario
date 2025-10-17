<?php
require_once '../config/db.php';
include '../includes/header_public.php'; // Usaremos un header público sin menú de admin

// Validar que se haya proporcionado un ID de partido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirigir o mostrar un error
    header("Location: ../index.php");
    exit();
}

$id_partido = $_GET['id'];

// 1. Obtener información del partido
$stmt_partido = $pdo->prepare("
    SELECT 
        p.*,
        c.nombre_campeonato,
        el.nombre_equipo as equipo_local, el.logo_equipo as logo_local,
        ev.nombre_equipo as equipo_visitante, ev.logo_equipo as logo_visitante
    FROM partidos p
    JOIN campeonatos c ON p.id_campeonato = c.id_campeonato
    JOIN equipos el ON p.id_equipo_local = el.id_equipo
    JOIN equipos ev ON p.id_equipo_visitante = ev.id_equipo
    WHERE p.id_partido = ?
");
$stmt_partido->execute([$id_partido]);
$partido = $stmt_partido->fetch(PDO::FETCH_ASSOC);

if (!$partido) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Partido no encontrado.</div></div>";
    include '../includes/footer.php';
    exit();
}

// 2. Obtener jugadores del equipo local
$stmt_jugadores_local = $pdo->prepare("SELECT * FROM jugadores WHERE id_equipo = ? ORDER BY nombre_jugador");
$stmt_jugadores_local->execute([$partido['id_equipo_local']]);
$jugadores_local = $stmt_jugadores_local->fetchAll(PDO::FETCH_ASSOC);

// 3. Obtener jugadores del equipo visitante
$stmt_jugadores_visitante = $pdo->prepare("SELECT * FROM jugadores WHERE id_equipo = ? ORDER BY nombre_jugador");
$stmt_jugadores_visitante->execute([$partido['id_equipo_visitante']]);
$jugadores_visitante = $stmt_jugadores_visitante->fetchAll(PDO::FETCH_ASSOC);

function mostrar_jugador($jugador, $delay) {
    $foto = !empty($jugador['foto_jugador']) ? '../' . htmlspecialchars($jugador['foto_jugador']) : '../uploads/jugadores/default.png';
    return '
        <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="' . $delay . '">
            <div class="card h-100 player-card">
                <img src="' . $foto . '" class="card-img-top" alt="Foto de ' . htmlspecialchars($jugador['nombre_jugador']) . '">
                <div class="card-body text-center">
                    <h5 class="card-title">' . htmlspecialchars($jugador['nombre_jugador']) . '</h5>
                    <h6 class="card-subtitle mb-2 text-muted">' . htmlspecialchars($jugador['posicion']) . '</h6>
                    <p class="card-text small">' . htmlspecialchars($jugador['descripcion']) . '</p>
                </div>
            </div>
        </div>
    ';
}
?>

<style>
    .match-hero {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://source.unsplash.com/1600x600/?soccer,field') no-repeat center center;
        background-size: cover;
        padding: 4rem 0;
        color: #fff;
    }
    .team-logo {
        max-width: 100px;
        height: auto;
        margin-bottom: 1rem;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.4));
    }
    .score {
        font-family: 'Montserrat', sans-serif;
        font-size: 4.5rem;
        font-weight: 700;
        text-shadow: 0 0 20px rgba(255,255,255,0.4);
    }
    .team-name {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 2rem;
    }
    .match-details {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 1rem;
        display: inline-block;
    }
    .player-card {
        border: 0;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: transform .3s, box-shadow .3s;
        overflow: hidden;
    }
    .player-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    .player-card img {
        height: 280px;
        object-fit: cover;
        object-position: top;
    }
    .player-card .card-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
    }
    .section-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        text-align: center;
        margin-bottom: 3rem;
        font-size: 2.5rem;
        background: linear-gradient(45deg, #FF4D00, #FF8C00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<div class="match-hero text-center">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-4 text-center" data-aos="fade-right">
                <img src="../<?php echo htmlspecialchars($partido['logo_local']); ?>" class="team-logo" alt="Logo <?php echo htmlspecialchars($partido['equipo_local']); ?>">
                <h2 class="team-name"><?php echo htmlspecialchars($partido['equipo_local']); ?></h2>
            </div>
            <div class="col-md-2 text-center" data-aos="zoom-in">
                <span class="score"><?php echo $partido['resultado_local'] ?? '-'; ?>:<?php echo $partido['resultado_visitante'] ?? '-'; ?></span>
            </div>
            <div class="col-md-4 text-center" data-aos="fade-left">
                <img src="../<?php echo htmlspecialchars($partido['logo_visitante']); ?>" class="team-logo" alt="Logo <?php echo htmlspecialchars($partido['equipo_visitante']); ?>">
                <h2 class="team-name"><?php echo htmlspecialchars($partido['equipo_visitante']); ?></h2>
            </div>
        </div>
        <div class="mt-4" data-aos="fade-up" data-aos-delay="200">
            <div class="match-details">
                <strong><?php echo htmlspecialchars($partido['nombre_campeonato']); ?></strong><br>
                <small><?php echo date('d F, Y - H:i', strtotime($partido['fecha_partido'])); ?> | <?php echo htmlspecialchars($partido['lugar']); ?></small>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <h2 class="section-title" data-aos="fade-up">ALINEACIONES</h2>

    <div class="row">
        <!-- Equipo Local -->
        <div class="col-lg-6 mb-5 mb-lg-0">
            <h3 class="mb-4 text-center" data-aos="fade-up"><?php echo htmlspecialchars($partido['equipo_local']); ?></h3>
            <div class="row">
                <?php if (empty($jugadores_local)): ?>
                    <p class="text-center text-muted">No hay jugadores registrados para este equipo.</p>
                <?php else: ?>
                    <?php foreach ($jugadores_local as $index => $jugador) echo mostrar_jugador($jugador, $index * 100); ?>
                <?php endif; ?>
            </div>
        </div>
        <!-- Equipo Visitante -->
        <div class="col-lg-6">
            <h3 class="mb-4 text-center" data-aos="fade-up"><?php echo htmlspecialchars($partido['equipo_visitante']); ?></h3>
            <div class="row">
                <?php if (empty($jugadores_visitante)): ?>
                    <p class="text-center text-muted">No hay jugadores registrados para este equipo.</p>
                <?php else: ?>
                    <?php foreach ($jugadores_visitante as $index => $jugador) echo mostrar_jugador($jugador, $index * 100); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        offset: 50
    });
</script>

<?php include '../includes/footer.php'; ?>


<?php include '../includes/footer.php'; ?>
