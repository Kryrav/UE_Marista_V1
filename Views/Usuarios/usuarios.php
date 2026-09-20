<?php 
    headerAdmin($data); 
    getModal('modalUsuarios', $data);
?>
<main class="app-content">    
    <div class="app-title">
        <div>
            <h1>
                <i class="fas fa-user-tag"></i> <?= $data['page_title'] ?>
                <?php if ($_SESSION['permisosMod']['w']) { ?>
                <button class="btn btn-primary" type="button" onclick="openModal();" data-toggle="tooltip" title="Nuevo usuario">
                    <i class="fas fa-plus-circle"></i> Nuevo
                </button>
                <?php } ?>
            </h1>
            <p>Gestión de usuarios del sistema</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/usuarios">Usuarios</a></li>
        </ul>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <!-- Filtros rápidos -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="tableUsuarios.ajax.reload();">
                                    <i class="fas fa-sync-alt"></i> Actualizar
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportToExcel();">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportToPDF();">
                                    <i class="fas fa-file-pdf"></i> Exportar PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de usuarios -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableUsuarios" width="100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th>CI</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargan vía AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal para ver usuario (ya está en modalUsuarios.php) -->
<?php footerAdmin($data); ?>