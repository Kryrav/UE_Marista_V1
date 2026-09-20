<?php 
    headerAdmin($data); 
    getModal('modalCursos',$data);
?>
  <main class="app-content">    
        <div class="app-title">
            <div>
                <h1><i class="fas fa-user-tag"></i> <?= $data['page_title'] ?>
                    <?php if($_SESSION['permisosMod']['w']){ ?>
                    <button class="btn btn-primary" type="button" onclick="window.print();" ><i class="fas fa-plus-circle"></i>  Imprimir lista </button>
                    <?php } ?>
                </h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/Cursos"><?= $data['page_title'] ?></a></li>
            
            </ul>
        </div>

        
        <div class="modal-body">
        <div class="row">
            <div class="col-md-12 text-align-center">
                <div class="tile m-1">
                    <div class="tile-body ">
                        <div class="card ">
                            <div class="card-header text-center">
                                <h5 class="mb-0">Lista de Estudiantes Matriculados</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Datos generales del curso</th>
                                            <th colspan="2">Información adicional</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="p-1">
                                            <td><strong>Curso:</strong></td>
                                            <td class="text-center"><?= $data['curso']['nivel'].': ' ?> <?= $data['curso']['grado'].' ' ?><?= $data['curso']['sigla'].' ' ?></td>
                                            <td><strong>Estado:</strong></td>
                                            <td class="text-center"><?= $data['curso']['status_paralelo'].' ' ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Turno:</strong></td>
                                            <td class="text-center"><?= $data['curso']['turno'].' ' ?></td>
                                            <td><strong>Tutor:</strong></td>
                                            <td class="text-center"><?= $data['curso']['tutor'].' ' ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cantidad de Estudiantes:</strong></td>
                                            <td class="text-center"><?= $data['curso']['total_inscritos'].' ' ?></td>
                                            <td><strong>Cupo:</strong></td>
                                            <td class="text-center"><?= $data['curso']['cupo'].' ' ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                        <h4>Estudiantes Matriculados</h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="tableEstudiantes">
                                <thead>
                                    <tr>
                                    <th>CI</th>
                                    <th>Matricula</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Acceso al Sistema</th>
                                    
                                    </tr>
                                </thead>
                                <tbody id="listCursoEstudiantes">
                                <?= $data['estudiante'] ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>

        
    </main>
<?php footerAdmin($data); ?>
    