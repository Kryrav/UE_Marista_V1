<?php 
    headerAdmin($data); 
    getModal('modalMatricula',$data);
?>
  <main class="app-content">    
      <div class="app-title">
        <div>
            <h1><i class="fas fa-user-tag"></i> <?= $data['page_title'] ?>
                <?php if($_SESSION['permisosMod']['w']){ ?>
                <button class="btn btn-primary" type="button" onclick="openModal();" ><i class="fas fa-plus-circle"></i> Matricular Estudiante</button>
                <?php } ?>
                <?php if(!empty($data['gestion_activa'])){ ?>
                <span class="badge badge-success ml-2" id="badgeGestionActiva" data-gestion="<?= (int)$data['gestion_activa'] ?>"><i class="fa fa-calendar"></i> Gestión activa: <?= (int)$data['gestion_activa'] ?></span>
                <?php } ?>
            </h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="<?= base_url(); ?>/Matricula"><?= $data['page_title'] ?></a></li>
        </ul>
      </div>
        <div class="row">
            <div class="col-md-12">
              <div class="tile">
                <div class="tile-body">
                  <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="tableMatricula">
                      <thead>
                        <tr>
                          <th>Año Lectivo</th>
                          <th>Matricula</th>
                          <th>CI</th>
                          <th>Nombre</th>
                          <th>Apellido</th>
                          <th>Curso</th>
                          <th>Registro</th>
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
<?php footerAdmin($data); ?>
    