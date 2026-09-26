<?php 
    headerAdmin($data); 
    getModal('modalEstudiantes',$data); 
?>
<link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/estudiantes.css">
<link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/tutores.css">
  <main class="app-content">    
      <div class="app-title">
        <div>
                <h1><i class="fa fa-graduation-cap"></i> <?= $data['page_title'] ?>
                <?php if($_SESSION['permisosMod']['w']){ ?>
                <button class="btn btn-primary" type="button" onclick="openModal();" ><i class="fas fa-plus-circle"></i> Nuevo</button>
              <?php } ?>
                <a class="btn btn-warning ml-2" target="_blank" href="<?= base_url(); ?>/Estudiantes/rezagados" title="Reporte de rezago 2+ años para Comisión Técnica"><i class="fa fa-exclamation-triangle"></i> Rezago</a>
            </h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="<?= base_url(); ?>/Estudiantes"><?= $data['page_title'] ?></a></li>
        </ul>
      </div>

        <div class="tile est-toolbar">
            <div class="row align-items-end">
                <div class="col-md-4 col-sm-6 mb-2">
                    <label for="filtroCursoEst"><strong>Curso actual</strong></label>
                    <select id="filtroCursoEst" class="form-control form-control-sm">
                        <option value="">Todos los cursos</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label for="filtroEstadoEst"><strong>Estado</strong></label>
                    <select id="filtroEstadoEst" class="form-control form-control-sm">
                        <option value="todos">Todos</option>
                        <option value="1">Activos</option>
                        <option value="2">Inactivos</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 mb-2">
                    <label for="chkSinFolio"><strong>Legajo</strong></label>
                    <div class="form-check mt-1">
                        <input type="checkbox" class="form-check-input" id="chkSinFolio" value="1">
                        <label class="form-check-label" for="chkSinFolio"><small>Solo sin folio</small></label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 mb-2">
                    <p class="est-resumen mb-0" id="resumenEst">Cargando estudiantes...</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
              <div class="tile">
                <div class="tile-body">
                  <div class="table-responsive">
                    <table class="table table-hover table-bordered w-100" id="tableEstudiantes">
                      <thead>
                        <tr>
                          <th>Foto</th>
                          <th>CI</th>
                          <th>Folio</th>
                          <th>Nombres</th>
                          <th>Apellidos</th>
                          <th>Curso actual</th>
                          <th>Tutores</th>
                          <th>Email</th>
                          <th>Celular</th>
                          <th>Estado</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
    </main>
<?php footerAdmin($data); ?>
