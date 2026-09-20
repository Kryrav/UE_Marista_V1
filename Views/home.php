<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['page_tag'] ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?= media(); ?>/css/styleHome.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= media(); ?>/images/favicon.ico">
    
    <meta name="description" content="<?= htmlspecialchars($data['page_description'] ?? 'Colegio Marista Sagrados Corazones') ?>">
    <meta name="keywords" content="colegio, marista, educación, roboré, bolivia, primaria, secundaria">
</head>
<body>
    <!-- Navbar Institucional -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">
                <img src="<?= media(); ?>/images/logo-colegio.png" alt="Logo Colegio Marista" height="50">
                <span class="ms-2 d-none d-md-inline"><?= $data['colegio']['nombre_col'] ?? 'Colegio Marista' ?></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="<?= base_url() ?>">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url() ?>/home/about">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url() ?>/home/admissions">Admisiones</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url() ?>/home/contact">Contacto</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-light text-primary ms-2" href="<?= base_url() ?>/login">Acceso Sistema</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white mb-4"><?= $data['colegio']['nombre_col'] ?? 'Colegio Marista SS.CC.' ?></h1>
                    <p class="lead text-white mb-4"><?= $data['colegio']['descripcion_col'] ?? 'Educación de excelencia basada en valores maristas' ?></p>
                    <a href="<?= base_url() ?>/home/admissions" class="btn btn-light btn-lg me-3">Admisiones <?= $data['gestionActiva']['gestion'] ?? date('Y') ?></a>
                    <a href="<?= base_url() ?>/home/contact" class="btn btn-outline-light btn-lg">Contáctanos</a>
                </div>
                <div class="col-lg-6">
                    <img src="<?= media(); ?>/images/colegio-hero.jpg" alt="Colegio Marista" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Estadísticas -->
    <section class="stats-section py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-card p-4 rounded shadow-sm">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold"><?= number_format($data['estadisticas']['total_estudiantes'] ?? 0) ?>+</h3>
                        <p class="text-muted">Estudiantes</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-card p-4 rounded shadow-sm">
                        <i class="fas fa-chalkboard-teacher fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold"><?= number_format($data['estadisticas']['total_docentes'] ?? 0) ?>+</h3>
                        <p class="text-muted">Docentes</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-card p-4 rounded shadow-sm">
                        <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold"><?= number_format($data['estadisticas']['total_cursos'] ?? 0) ?>+</h3>
                        <p class="text-muted">Cursos</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-card p-4 rounded shadow-sm">
                        <i class="fas fa-book fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold"><?= number_format($data['estadisticas']['total_materias'] ?? 0) ?>+</h3>
                        <p class="text-muted">Materias</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cursos Destacados -->
    <section class="courses-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Nuestros Cursos Destacados</h2>
            <div class="row">
                <?php if (!empty($data['cursosDestacados'])): ?>
                    <?php foreach ($data['cursosDestacados'] as $curso): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card course-card h-100 shadow">
                            <div class="card-header bg-primary text-white text-center">
                                <h4 class="mb-0"><?= htmlspecialchars($curso['nivel']) ?> - <?= htmlspecialchars($curso['grado']) ?><?= htmlspecialchars($curso['sigla']) ?></h4>
                            </div>
                            <div class="card-body">
                                <p><strong>Tutor:</strong> <?= htmlspecialchars($curso['tutor']) ?></p>
                                <p><strong>Estudiantes:</strong> <?= $curso['total_inscritos'] ?></p>
                                <div class="progress mb-3">
                                    <div class="progress-bar" style="width: <?= min(($curso['total_inscritos'] / 30) * 100, 100) ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No hay cursos disponibles</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Noticias -->
    <section class="news-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Últimas Noticias</h2>
            <div class="row">
                <?php if (!empty($data['noticias'])): ?>
                    <?php foreach ($data['noticias'] as $noticia): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card news-card h-100 shadow">
                            <?php if ($noticia['imagen']): ?>
                            <img src="<?= media() ?>/uploads/noticias/<?= htmlspecialchars($noticia['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($noticia['titulo']) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($noticia['titulo']) ?></h5>
                                <p class="card-text"><?= substr(htmlspecialchars($noticia['contenido']), 0, 150) ?>...</p>
                                <small class="text-muted"><?= date('d/m/Y', strtotime($noticia['fecha_publicacion'])) ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">Próximamente noticias</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Testimonios -->
    <section class="testimonials-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Lo que dicen nuestros padres</h2>
            <div class="row">
                <?php if (!empty($data['testimonios'])): ?>
                    <?php foreach ($data['testimonios'] as $testimonio): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card testimonial-card h-100 shadow">
                            <div class="card-body text-center">
                                <?php if ($testimonio['foto']): ?>
                                <img src="<?= media() ?>/uploads/testimonios/<?= htmlspecialchars($testimonio['foto']) ?>" class="rounded-circle mb-3" width="80" height="80" alt="<?= htmlspecialchars($testimonio['nombre']) ?>">
                                <?php endif; ?>
                                <p class="card-text">"<?= htmlspecialchars($testimonio['testimonio']) ?>"</p>
                                <h5 class="card-title"><?= htmlspecialchars($testimonio['nombre']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($testimonio['cargo']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">Testimonios próximamente</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4><?= $data['colegio']['nombre_col'] ?? 'Colegio Marista SS.CC.' ?></h4>
                    <p><?= $data['colegio']['descripcion_col'] ?? 'Institución educativa de excelencia' ?></p>
                    <div class="social-icons mt-3">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-2x"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <h4>Contacto</h4>
                    <p><i class="fas fa-map-marker-alt me-2"></i> <?= $data['colegio']['direccion_col'] ?? 'Av. Principal, Roboré' ?></p>
                    <p><i class="fas fa-phone me-2"></i> <?= $data['colegio']['telefono_col'] ?? '9742039' ?></p>
                    <p><i class="fas fa-envelope me-2"></i> <?= $data['colegio']['correo_col'] ?? 'info@maristarobore.bo' ?></p>
                    <p><i class="fas fa-clock me-2"></i> <?= $data['colegio']['horario_atencion_col'] ?? 'Lunes a Viernes, 7:00 - 12:00' ?></p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h4>Enlaces Rápidos</h4>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url() ?>" class="text-white text-decoration-none">Inicio</a></li>
                        <li><a href="<?= base_url() ?>/home/about" class="text-white text-decoration-none">Nosotros</a></li>
                        <li><a href="<?= base_url() ?>/home/admissions" class="text-white text-decoration-none">Admisiones</a></li>
                        <li><a href="<?= base_url() ?>/home/contact" class="text-white text-decoration-none">Contacto</a></li>
                        <li><a href="<?= base_url() ?>/login" class="text-white text-decoration-none">Acceso al Sistema</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-white">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= $data['anio_actual'] ?> <?= $data['colegio']['nombre_col'] ?? 'Colegio Marista SS.CC.' ?>. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>Desarrollado por <a href="#" class="text-white">Equipo de Sistemas</a></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= media(); ?>/js/home.js"></script>
</body>
</html>