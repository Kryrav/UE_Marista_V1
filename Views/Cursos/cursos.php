<?php 
    headerAdmin($data); 
    getModal('modalCursos',$data);
?>
<link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/cursos.css">
  <main class="app-content">    
        <div class="app-title">
            <div>
                <h1><i class="fas fa-chalkboard-teacher"></i> <?= $data['page_title'] ?>
                    <?php if($_SESSION['permisosMod']['w']){ ?>
                    <button class="btn btn-primary" type="button" onclick="openModalCurso();" ><i class="fas fa-plus-circle"></i> Nuevo Paralelo</button>
                    <?php } ?>
                </h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/Cursos"><?= $data['page_title'] ?></a></li>
            </ul>
        </div>

        <div class="tile curso-toolbar">
            <div class="row align-items-end">
                <div class="col-md-2 col-sm-6 mb-2">
                    <label for="selGestionCurso"><strong>Gestión</strong></label>
                    <select id="selGestionCurso" class="form-control form-control-sm">
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                    </select>
                </div>
                <div class="col-md-4 col-sm-6 mb-2">
                    <label for="buscadorCurso"><strong>Buscar</strong></label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div>
                        <input type="text" id="buscadorCurso" class="form-control" placeholder="Nivel, grado, sigla o tutor..." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label for="filtroNivel"><strong>Nivel</strong></label>
                    <select id="filtroNivel" class="form-control form-control-sm">
                        <option value="todos">Todos los niveles</option>
                        <option value="Inicial">Inicial</option>
                        <option value="Primaria">Primaria</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label for="filtroEstado"><strong>Estado</strong></label>
                    <select id="filtroEstado" class="form-control form-control-sm">
                        <option value="todos">Todos</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <p class="curso-resumen mb-0" id="resumenCursos">Cargando paralelos...</p>
                </div>
            </div>
        </div>

        <div class="row" id="CursosCard"></div>
        <div id="sinResultados" class="curso-empty" style="display:none;">
            <i class="fa fa-search fa-3x"></i>
            <h5>Sin resultados</h5>
            <p class="mb-0">Ningún paralelo coincide con los filtros aplicados.</p>
        </div>

    </main>
<?php footerAdmin($data); ?>
