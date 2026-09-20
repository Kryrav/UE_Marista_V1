
<?php 
    headerAdmin($data); 
    getModal('modalGestion',$data);
?>
    <main class="app-content">    

        <div class="app-title">
            <div>
                <h1><i class="fa fa-list-alt"></i> <?= $data['page_title'] ?>
                    <?php if($_SESSION['permisosMod']['w']){ ?>
                    <button class="btn btn-primary btn-OpenGestion" type="button" onclick="openNewGestion();" ><i class="fas fa-plus-circle"></i> Abrir Nueva Gestión</button>
                    <?php } ?>
                </h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
                <li class="breadcrumb-item"><a href="<?= base_url(); ?>/materias"><?= $data['page_title'] ?></a></li>
            </ul>
        </div>
        
        <div class="tile" >
            <div class="tile-body ">
                <div class="mt-1">
                    <div class="row">
                        <div class="col-md-3">
                        <!-- Menú de selección -->
                            <ul class="nav flex-column nav-pills">
                                <li class="nav-item">
                                <a class="nav-link active" id="actual-tab" data-toggle="pill" href="#actual" role="tab" aria-controls="actual" aria-selected="true">Gestión Actual</a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link" id="pasados-tab" data-toggle="pill" href="#pasados" role="tab" aria-controls="pasados" aria-selected="false">Gestiones Registradas</a>
                                </li>
                            </ul>
                        </div>

            
                        <div class="col-md-9">
                        <!-- Contenido de las secciones -->
                            <div class="tab-content">
                                <!-- Año Lectivo Actual -->
                                <div class="tab-pane fade show active" id="actual" role="tabpanel" aria-labelledby="actual-tab">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="section-title">AÑO LECTIVO ACTIVO</h5>
                                            <table class="table table-borderless mt-1" id="gestion_actual">
                                                <tr>
                                                    <th>Año:</th>
                                                    <td id="cellGestion">*</td>
                                                </tr>
                                                <tr>
                                                    <th>Fecha de Inicio:</th>
                                                    <td id="cellInicio">*</td>
                                                </tr>
                                                <tr>
                                                    <th>Fecha de Culminación:</th>
                                                    <td id="cellFin">*</td>
                                                </tr>
                                                <tr>
                                                    <th>Costo de Mensualidad (Bs.):</th>
                                                    <td id="cellPension">*</td>
                                                </tr>
                                                <tr>
                                                    <th>Descripcion / Nota:</th>
                                                    <td id="cellDescripcion">*</td>
                                                </tr>
                                                <tr>
                                                    <th>Estado:</th>
                                                    <td id="cellStatus">*</td>
                                                </tr>
                                            </table>
                                            <?php if($_SESSION['permisosMod']['u']){ ?>
                                            <button class="btn btn-primary btn-updateGestion" type="button" onclick="updateGestion();" ><i class="fa fa-pencil-square"></i>Modificar Datos</button>
                                            <button class="btn btn-secondary btn-closeGestion" type="button" onclick="closeGestion();" ><i class="fa fa-window-close"></i>Cerrar Gestión</button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Años Lectivos Pasados -->
                                <div class="tab-pane fade" id="pasados" role="tabpanel" aria-labelledby="pasados-tab">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="section-title">GESTIONES REGISTRADAS</h5>
                                            <table class="table table-hover table-bordered" id="tableGestiones">
                                                <thead>
                                                    <tr>
                                                        <th>Año</th>
                                                        <th>Fecha de Inicio</th>
                                                        <th>Fecha de Fin</th>
                                                        <th>Mensualidad (Bs.)</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                               
                                                <!-- Agregar más años lectivos pasados según sea necesario -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
    </main>


<?php footerAdmin($data); ?>