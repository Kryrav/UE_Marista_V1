<?php 
    headerAdmin($data); 
    getModal('modalTutores',$data);
?>
<link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/tutores.css">
  <main class="app-content">    
      <div class="app-title">
        <div>
            <h1><i class="fa fa-users"></i> <?= $data['page_title'] ?>
                <?php if($_SESSION['permisosMod']['w']){ ?>
                <button class="btn btn-primary" type="button" onclick="openModalTutor();" ><i class="fas fa-plus-circle"></i> Nuevo Tutor</button>
              <?php } ?>
            </h1>
            <p class="text-muted">Registre padres/apoderados y vincúlelos a un estudiante. El tutor sirve como pagador de pensiones.</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="<?= base_url(); ?>/Tutores"><?= $data['page_title'] ?></a></li>
        </ul>
      </div>
        <div class="tile tut-toolbar">
            <div class="row align-items-end">
                <div class="col-md-4 col-sm-6 mb-2">
                    <label for="filtroParentesco"><strong>Parentesco</strong></label>
                    <select id="filtroParentesco" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        <option>Padre</option>
                        <option>Madre</option>
                        <option>Tutor</option>
                        <option>Tío</option>
                        <option>Tía</option>
                        <option>Hermano</option>
                        <option>Apoderado</option>
                    </select>
                </div>
                <div class="col-md-8 col-sm-6 mb-2">
                    <p class="est-resumen mb-0" id="resumenTut">Cargando tutores...</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
              <div class="tile">
                <div class="tile-body">
                  <div class="table-responsive">
                    <table class="table table-hover table-bordered w-100" id="tableTutores">
                      <thead>
                        <tr>
                          <th>CI Tutor</th>
                          <th>Nombre</th>
                          <th>Apellido</th>
                          <th>Parentesco</th>
                          <th>Estudiante</th>
                          <th>Celular</th>
                          <th>Status</th>
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

<!-- Vista completa del tutor -->
<div class="modal fade" id="modalTutoresEst" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header tut-header">
        <h5 class="modal-title" id="modalTutoresEstTitle"><i class="fa fa-user"></i> Tutor</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="listaTutoresEst"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php footerAdmin($data); ?>
