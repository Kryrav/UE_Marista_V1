<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidad Educativa Marista Sagrados Corazones I - Roboré</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?= media();?>/css/home_style.css">
    <link rel="shortcut icon" href="<?= media();?>/images/favicon.ico">


</head>
<body>
    <!-- Loading Animation -->
    <div class="loading" id="loading">
        <div class="loading-spinner"></div>
    </div>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Navbar Institucional -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#inicio">
                <img src="<?= media(); ?>/images/uploads/logoMarista.svg" alt="Logo Unidad Educativa Marista" height="50" onerror="this.src='https://via.placeholder.com/50x50/1a3b5d/ffffff?text=UEM'">
                <span class="ms-2 d-none d-md-inline">U.E. Marista Sagrados Corazones</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#inicio">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#nosotros">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="#valores">Valores</a></li>
                    <li class="nav-item"><a class="nav-link" href="#noticias">Noticias</a></li>
                    <li class="nav-item"><a class="nav-link" href="#testimonios">Testimonios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-light text-primary ms-2" href="<?=base_url()?>/login"">Acceso Sistema</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="inicio">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                    <span class="rue-badge mb-3">RUE: 61940001</span>
                    <h1 class="display-4 fw-bold text-white mb-4">Unidad Educativa Marista<br>Sagrados Corazones I</h1>
                    <p class="lead text-white mb-4">Formando buenos cristianos y honrados ciudadanos bajo el carisma de San Marcelino Champagnat</p>
                    <div class="mb-4">
                        <span class="info-badge"><i class="fas fa-map-marker-alt me-1"></i> Roboré, Santa Cruz</span>
                        <span class="info-badge"><i class="fas fa-school me-1"></i> Inicial y Primaria</span>
                        <span class="info-badge"><i class="fas fa-users me-1"></i> 343 Estudiantes</span>
                    </div>
                    <a href="#" class="btn btn-light btn-lg me-3 m-2"><i class="fas fa-graduation-cap me-2"></i>Proceso de Admisiones 2025</a>
                    <a href="#contacto" class="btn btn-outline-light btn-lg m-2"><i class="fas fa-phone-alt me-2"></i>Contáctanos</a>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000">
                    <div class="hero-image">
                        <img src="<?= media(); ?>/images/bannerPerfil.png" alt="Unidad Educativa Marista Roboré" class="img-fluid rounded shadow" onerror="this.src='https://via.placeholder.com/600x400/1a3b5d/ffffff?text=U.E.+Marista+Robor%C3%A9'">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Estadísticas -->
    <section class="stats-section py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="stat-card p-4 rounded shadow-sm">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold counter">343</h3>
                        <p class="text-muted">Estudiantes 2024</p>
                        <small class="text-muted">168 mujeres | 175 varones</small>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="stat-card p-4 rounded shadow-sm">
                        <i class="fas fa-chalkboard-teacher fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold counter">25</h3>
                        <p class="text-muted">Docentes</p>
                        <small class="text-muted">Personal calificado</small>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="stat-card p-4 rounded shadow-sm">
                        <i class="fas fa-layer-group fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold counter">2</h3>
                        <p class="text-muted">Niveles</p>
                        <small>Inicial y Primaria</small>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="400">
                    <div class="stat-card p-4 rounded shadow-sm">
                        <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold counter">40</h3>
                        <p class="text-muted">Años de historia</p>
                        <small>Desde 1984</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Misión y Visión -->
    <section id="nosotros" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Conoce Nuestra Institución</h2>
            <div class="row">
                <div class="col-lg-6 mb-4" data-aos="fade-right">
                    <div class="p-4 bg-white rounded shadow h-100">
                        <h3 class="text-primary mb-4"><i class="fas fa-bullseye me-2"></i>Misión</h3>
                        <p class="lead mb-4">Inspirados en el carisma de San Marcelino Champagnat, fundador de los Hermanos Maristas, nuestra misión es:</p>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Educar integralmente a niñas, niños y jóvenes</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Formar "buenos cristianos y honrados ciudadanos"</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Basar la enseñanza en el Evangelio de Jesucristo</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Promover valores maristas: jovialidad, afabilidad, constancia y humildad</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 mb-4" data-aos="fade-left">
                    <div class="p-4 bg-white rounded shadow h-100">
                        <h3 class="text-primary mb-4"><i class="fas fa-eye me-2"></i>Visión</h3>
                        <p class="lead mb-4">Nuestra visión se fundamenta en:</p>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Ser un espacio educativo que acompañe el crecimiento humano, espiritual y académico</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Favorecer la experiencia de la fe y el compromiso social</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Contribuir al desarrollo de la comunidad de Roboré mediante una educación inclusiva y de calidad</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Valores Fundamentales -->
    <section id="valores" class="values-section py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Nuestros Valores Fundamentales</h2>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="flip-left" data-aos-delay="100">
                    <div class="value-card">
                        <i class="fas fa-heart"></i>
                        <h4>Fe y Espiritualidad</h4>
                        <p class="text-muted">Formación cristiana basada en el Evangelio y el carisma marista</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="flip-left" data-aos-delay="200">
                    <div class="value-card">
                        <i class="fas fa-hand-holding-heart"></i>
                        <h4>Respeto y Solidaridad</h4>
                        <p class="text-muted">Convivencia armónica y compromiso con los demás</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="flip-left" data-aos-delay="300">
                    <div class="value-card">
                        <i class="fas fa-shield-alt"></i>
                        <h4>Responsabilidad Social</h4>
                        <p class="text-muted">Compromiso con la comunidad y el desarrollo local</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="flip-left" data-aos-delay="400">
                    <div class="value-card">
                        <i class="fas fa-gem"></i>
                        <h4>Jovialidad</h4>
                        <p class="text-muted">Alegría y optimismo en la vida cotidiana</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="flip-left" data-aos-delay="500">
                    <div class="value-card">
                        <i class="fas fa-dove"></i>
                        <h4>Afabilidad</h4>
                        <p class="text-muted">Trato amable y cordial con todos</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="flip-left" data-aos-delay="600">
                    <div class="value-card">
                        <i class="fas fa-tree"></i>
                        <h4>Humildad</h4>
                        <p class="text-muted">Sencillez y apertura de corazón</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Últimas Noticias -->
    <section id="noticias" class="news-section py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Últimas Noticias</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="news-card">
                        <div class="news-image">
                            <img src="<?= media();?>/images/notice/nt_1.jpg" alt="Acto Cívico">
                            <span class="news-date"><i class="far fa-calendar-alt me-1"></i>15 Mar 2024</span>
                        </div>
                        <div class="p-4">
                            <span class="news-category">Eventos</span>
                            <h3 class="news-title">Acto Cívico por el Día del Mar</h3>
                            <p class="text-muted mb-3">Estudiantes de nivel primario participaron en el acto cívico conmemorando el Día del Mar con presentaciones artísticas y culturales.</p>
                            <a href="#" class="read-more">Leer más <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="news-card">
                        <div class="news-image">
                            <img src="<?= media();?>/images/notice/nt_2.jpg" alt="Taller de Valores">
                            <span class="news-date"><i class="far fa-calendar-alt me-1"></i>10 Mar 2024</span>
                        </div>
                        <div class="p-4">
                            <span class="news-category">Formación</span>
                            <h3 class="news-title">Taller de Valores Maristas</h3>
                            <p class="text-muted mb-3">Estudiantes participaron en taller sobre la vida de San Marcelino Champagnat y los valores maristas aplicados a la vida cotidiana.</p>
                            <a href="#" class="read-more">Leer más <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="news-card">
                        <div class="news-image">
                            <img src="<?= media();?>/images/notice/nt_3.jpg" alt="Reunión de Padres">
                            <span class="news-date"><i class="far fa-calendar-alt me-1"></i>5 Mar 2024</span>
                        </div>
                        <div class="p-4">
                            <span class="news-category">Comunidad</span>
                            <h3 class="news-title">Reunión de Padres de Familia</h3>
                            <p class="text-muted mb-3">Se llevó a cabo la primera reunión de padres de familia del año escolar 2024, con una destacada participación de la comunidad.</p>
                            <a href="#" class="read-more">Leer más <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="news-card">
                        <div class="news-image">
                            <img src="<?= media();?>/images/notice/nt_4.jpg" alt="Inicio de Clases">
                            <span class="news-date"><i class="far fa-calendar-alt me-1"></i>1 Feb 2024</span>
                        </div>
                        <div class="p-4">
                            <span class="news-category">Académico</span>
                            <h3 class="news-title">Inicio de Clases Gestión 2024</h3>
                            <p class="text-muted mb-3">Damos la bienvenida a todos nuestros estudiantes para iniciar un nuevo año escolar lleno de aprendizajes y experiencias.</p>
                            <a href="#" class="read-more">Leer más <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="news-card">
                        <div class="news-image">
                            <img src="<?= media();?>/images/notice/nt_5.jpg" alt="Proyecto Educativo">
                            <span class="news-date"><i class="far fa-calendar-alt me-1"></i>20 Ene 2024</span>
                        </div>
                        <div class="p-4">
                            <span class="news-category">Innovación</span>
                            <h3 class="news-title">Nuevo Proyecto Educativo</h3>
                            <p class="text-muted mb-3">Implementamos nuevas estrategias pedagógicas para fortalecer el aprendizaje en nuestros estudiantes.</p>
                            <a href="#" class="read-more">Leer más <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="news-card">
                        <div class="news-image">
                            <img src="<?= media();?>/images/notice/nt_6.jpg" alt="Actividades Deportivas">
                            <span class="news-date"><i class="far fa-calendar-alt me-1"></i>15 Ene 2024</span>
                        </div>
                        <div class="p-4">
                            <span class="news-category">Deportes</span>
                            <h3 class="news-title">Juegos Deportivos Estudiantiles</h3>
                            <p class="text-muted mb-3">Nuestros estudiantes participaron en los juegos deportivos internos, promoviendo el compañerismo y la actividad física.</p>
                            <a href="#" class="read-more">Leer más <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4" data-aos="fade-up">
                <a href="#" class="btn btn-primary btn-lg"><i class="fas fa-newspaper me-2"></i>Ver todas las noticias</a>
            </div>
        </div>
    </section>

    <!-- Testimonios -->
    <section id="testimonios" class="testimonials-section py-5">
        <div class="container">
            <h2 class="text-center text-white mb-5" data-aos="fade-up">Lo que dicen nuestros padres de familia</h2>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-right" data-aos-delay="100">
                    <div class="testimonial-card">
                        <img src="<?= media();?>/images/testimonio/tes_1.jpg" alt="Padre de familia" class="testimonial-image">
                        <p class="testimonial-text">Estoy muy agradecida con la formación que reciben mis hijos en esta institución. Los valores maristas se reflejan en su comportamiento diario.</p>
                        <h5 class="testimonial-author">María Fernández</h5>
                        <p class="testimonial-role">Madre de familia</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card">
                        <img src="<?= media();?>/images/testimonio/tes_2.jpg" alt="Padre de familia" class="testimonial-image">
                        <p class="testimonial-text">Excelente nivel académico y sobre todo la calidez humana de los docentes. Mi hija ama venir al colegio cada día.</p>
                        <h5 class="testimonial-author">Juan Carlos Gutiérrez</h5>
                        <p class="testimonial-role">Padre de familia</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-left" data-aos-delay="300">
                    <div class="testimonial-card">
                        <img src="<?= media();?>/images/testimonio/tes_3.jpg" alt="Padre de familia" class="testimonial-image">
                        <p class="testimonial-text">La educación basada en valores y el acompañamiento espiritual han sido fundamentales en el desarrollo de mis hijos.</p>
                        <h5 class="testimonial-author">Rosa Méndez</h5>
                        <p class="testimonial-role">Madre de familia</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Niveles Educativos -->
    <section class="courses-section py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Niveles Educativos</h2>
            <div class="row">
                <div class="col-md-6 mb-4" data-aos="fade-right">
                    <div class="card course-card h-100 shadow">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="mb-0"><i class="fas fa-child me-2"></i>Nivel Inicial</h4>
                        </div>
                        <div class="card-body">
                            <p><strong><i class="fas fa-clock me-2"></i>Turno:</strong> Mañana</p>
                            <p><strong><i class="fas fa-birthday-cake me-2"></i>Edades:</strong> 4-5 años</p>
                            <p><strong><i class="fas fa-school me-2"></i>Cursos:</strong> Pre-kínder y Kínder</p>
                            <div class="progress mb-3">
                                <div class="progress-bar" style="width: 75%">Cupo: 75%</div>
                            </div>
                            <p class="text-muted">Formación inicial en valores y desarrollo psicomotriz</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4" data-aos="fade-left">
                    <div class="card course-card h-100 shadow">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Nivel Primario</h4>
                        </div>
                        <div class="card-body">
                            <p><strong><i class="fas fa-clock me-2"></i>Turno:</strong> Mañana</p>
                            <p><strong><i class="fas fa-book me-2"></i>Cursos:</strong> 1ro a 6to de Primaria</p>
                            <p><strong><i class="fas fa-star me-2"></i>Énfasis:</strong> Formación integral académica y en valores</p>
                            <div class="progress mb-3">
                                <div class="progress-bar" style="width: 90%">Cupo: 90%</div>
                            </div>
                            <p class="text-muted">Educación de calidad con enfoque marista</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Información Institucional -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4" data-aos="fade-right">
                    <div class="bg-white p-4 rounded shadow h-100">
                        <h3 class="text-primary mb-4"><i class="fas fa-university me-2"></i>Datos Institucionales</h3>
                        <ul class="list-unstyled">
                            <li class="mb-3"><strong><i class="fas fa-tag me-2"></i>Tipo:</strong> Unidad Educativa Fiscal</li>
                            <li class="mb-3"><strong><i class="fas fa-qrcode me-2"></i>Código RUE:</strong> 61940001</li>
                            <li class="mb-3"><strong><i class="fas fa-building me-2"></i>Dependencia:</strong> Fiscal</li>
                            <li class="mb-3"><strong><i class="fas fa-map-pin me-2"></i>Ubicación:</strong> Área rural, Distrito Educativo Roboré</li>
                            <li class="mb-3"><strong><i class="fas fa-layers me-2"></i>Niveles:</strong> Inicial y Primaria</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 mb-4" data-aos="fade-left">
                    <div class="bg-white p-4 rounded shadow h-100">
                        <h3 class="text-primary mb-4"><i class="fas fa-chart-line me-2"></i>Matrícula 2024</h3>
                        <div class="d-flex justify-content-around text-center mb-4">
                            <div>
                                <h4 class="text-primary">168</h4>
                                <p>Mujeres</p>
                            </div>
                            <div>
                                <h4 class="text-primary">175</h4>
                                <p>Varones</p>
                            </div>
                            <div>
                                <h4 class="text-primary">343</h4>
                                <p>Total</p>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar bg-primary" style="width: 49%">Mujeres 49%</div>
                            <div class="progress-bar bg-secondary" style="width: 51%">Varones 51%</div>
                        </div>
                        <p class="text-muted mt-3"><i class="fas fa-info-circle me-2"></i>Distribución equitativa que refleja nuestro compromiso con la educación inclusiva.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section id="contacto" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Información de Contacto</h2>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="flip-up" data-aos-delay="100">
                    <div class="contact-info text-center">
                        <i class="fas fa-map-marker-alt"></i>
                        <h5>Dirección</h5>
                        <p>Calle España Nro. 51<br>Roboré, Provincia Chiquitos<br>Santa Cruz - Bolivia</p>
                        <a href="https://maps.google.com" target="_blank" class="btn btn-outline-primary btn-sm">Ver en mapa <i class="fas fa-external-link-alt ms-1"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="flip-up" data-aos-delay="200">
                    <div class="contact-info text-center">
                        <i class="fas fa-phone"></i>
                        <h5>Teléfonos</h5>
                        <p class="mb-1">39742039</p>
                        <p>74939940</p>
                        <a href="tel:39742039" class="btn btn-outline-primary btn-sm">Llamar ahora <i class="fas fa-phone-alt ms-1"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="flip-up" data-aos-delay="300">
                    <div class="contact-info text-center">
                        <i class="fas fa-clock"></i>
                        <h5>Horario de Atención</h5>
                        <p>Lunes a Viernes<br>7:30 - 12:30</p>
                        <a href="#" class="btn btn-outline-primary btn-sm">Agendar visita <i class="fas fa-calendar-check ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4" data-aos="fade-right">
                    <h4>Unidad Educativa Marista<br>Sagrados Corazones I</h4>
                    <p>Educación de excelencia basada en valores maristas, formando buenos cristianos y honrados ciudadanos desde 1984</p>
                    <p class="mt-3"><strong><i class="fas fa-qrcode me-2"></i>Código RUE:</strong> 61940001</p>
                    <div class="social-icons mt-3">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-2x"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up">
                    <h4>Contacto</h4>
                    <p><i class="fas fa-map-marker-alt me-2"></i> Calle España Nro. 51, Roboré, Santa Cruz</p>
                    <p><i class="fas fa-phone me-2"></i> 39742039 / 74939940</p>
                    <p><i class="fas fa-envelope me-2"></i> info@maristarobore.bo</p>
                    <p><i class="fas fa-clock me-2"></i> Lunes a Viernes, 7:30 - 12:30</p>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-left">
                    <h4>Enlaces Rápidos</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#inicio" class="text-white text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Inicio</a></li>
                        <li class="mb-2"><a href="#nosotros" class="text-white text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Nosotros</a></li>
                        <li class="mb-2"><a href="#valores" class="text-white text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Valores</a></li>
                        <li class="mb-2"><a href="#noticias" class="text-white text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Noticias</a></li>
                        <li class="mb-2"><a href="#contacto" class="text-white text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Contacto</a></li>
                        <li class="mb-2"><a href="<?=base_url()?>/login" class="text-white text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Acceso al Sistema</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-white opacity-25">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2024 Unidad Educativa Marista Sagrados Corazones I. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p><i class="fas fa-map-marker-alt me-2"></i>Distrito Educativo Roboré - Santa Cruz - Bolivia</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
        // Loading animation - CORREGIDO
        document.addEventListener('DOMContentLoaded', function() {
            // Forzar la desaparición del loading después de 1 segundo
            setTimeout(function() {
                const loading = document.getElementById('loading');
                if (loading) {
                    loading.classList.add('fade-out');
                    console.log('Loading ocultado correctamente');
                }
            }, 800);
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('mainNav');
            const backToTop = document.getElementById('backToTop');
            
            if (window.scrollY > 100) {
                nav.classList.add('scrolled');
                backToTop.classList.add('show');
            } else {
                nav.classList.remove('scrolled');
                backToTop.classList.remove('show');
            }
        });

        // Back to top button
        document.getElementById('backToTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Close mobile menu if open
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse.classList.contains('show')) {
                        navbarCollapse.classList.remove('show');
                    }
                }
            });
        });

        // Counter animation
        const counters = document.querySelectorAll('.counter');
        const speed = 200;

        const animateCounter = (counter) => {
            const target = parseInt(counter.innerText);
            let count = 0;
            const increment = target / speed;

            const updateCount = () => {
                if (count < target) {
                    count += increment;
                    counter.innerText = Math.ceil(count);
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target;
                }
            };

            updateCount();
        };

        // Intersection Observer for counters
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.counter');
                    counters.forEach(counter => animateCounter(counter));
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.stats-section').forEach(section => {
            observer.observe(section);
        });

        // Active nav link on scroll
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.nav-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (window.scrollY >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
