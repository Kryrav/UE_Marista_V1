<?php
    headerAdmin($data);
    getModal('modalPerfil', $data);
?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fas fa-user-circle"></i> <?= $data['page_title'] ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/perfil">Perfil</a></li>
        </ul>
    </div>

    <div class="row user">
        <div class="col-md-12">
            <div class="profile">
                <div class="info">
                    <img class="user-img" src="<?= media(); ?>/images/avatar.png">
                    <h4><?= $_SESSION['userData']['nombre'].' '.$_SESSION['userData']['apellido']; ?></h4>
                    <p><?= $_SESSION['userData']['nombrerol']; ?></p>
                </div>
                <div class="cover-image"></div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="tile p-0">
                <ul class="nav flex-column nav-tabs user-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#user-timeline" data-toggle="tab">
                            <i class="fas fa-user mr-2"></i>Datos personales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#user-settings" data-toggle="tab">
                            <i class="fas fa-cog mr-2"></i>Configuración
                        </a>
                    </li>
                    <?php if ($_SESSION['userData']['idrol'] == 7) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#user-pensiones" onclick="buscarPensionesCi(<?= $_SESSION['userData']['ci']; ?>)" data-toggle="tab">
                            <i class="fas fa-money-bill-alt mr-2"></i>Consultar pensiones
                        </a>
                    </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#user-security" data-toggle="tab">
                            <i class="fas fa-shield-alt mr-2"></i>Seguridad
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Datos Personales panel -->
                <!-- Panel: Datos personales -->
                <div class="tab-pane active" id="user-timeline">
                    <div class="tile">
                        <div class="tile-header d-flex justify-content-between align-items-center">
                            <h4 class="line-head mb-0">DATOS PERSONALES</h4>
                            <button class="btn btn-sm btn-outline-primary" onclick="editarDatosPersonales()">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                        <div class="tile-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered table-hover">
                                        <tbody>
                                            <tr>
                                                <td style="width:180px;"><strong><i class="fas fa-id-card text-primary mr-2"></i>C.I.:</strong></td>
                                                <td><?= $_SESSION['userData']['ci']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-user text-primary mr-2"></i>Nombres:</strong></td>
                                                <td><?= $_SESSION['userData']['nombre']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-user text-primary mr-2"></i>Apellidos:</strong></td>
                                                <td><?= $_SESSION['userData']['apellido']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-venus-mars text-primary mr-2"></i>Sexo:</strong></td>
                                                <td>
                                                    <?php 
                                                    $sexo = $_SESSION['userData']['sexo'] ?? '';
                                                    echo $sexo == 'M' ? '<span class="badge badge-info"><i class="fas fa-mars"></i> Masculino</span>' : 
                                                        ($sexo == 'F' ? '<span class="badge badge-danger"><i class="fas fa-venus"></i> Femenino</span>' : '-');
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-phone text-primary mr-2"></i>Teléfono:</strong></td>
                                                <td>
                                                    <a href="tel:<?= $_SESSION['userData']['cel']; ?>" class="text-decoration-none">
                                                        <i class="fas fa-phone-alt text-success mr-1"></i><?= $_SESSION['userData']['cel']; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered table-hover">
                                        <tbody>
                                            <tr>
                                                <td style="width:180px;"><strong><i class="fas fa-envelope text-primary mr-2"></i>Email:</strong></td>
                                                <td>
                                                    <a href="mailto:<?= $_SESSION['userData']['email']; ?>" class="text-decoration-none">
                                                        <i class="fas fa-envelope text-info mr-1"></i><?= $_SESSION['userData']['email']; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-map-marker-alt text-primary mr-2"></i>Dirección:</strong></td>
                                                <td><?= $_SESSION['userData']['direccion_dom'] ?? '-'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-calendar-alt text-primary mr-2"></i>Fecha Registro:</strong></td>
                                                <td>
                                                    <?= date('d/m/Y H:i', strtotime($_SESSION['userData']['fecha_reg'] ?? date('Y-m-d'))); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-user-tag text-primary mr-2"></i>Tipo de Usuario:</strong></td>
                                                <td>
                                                    <span class="badge badge-primary badge-pill p-2">
                                                        <i class="fas fa-id-badge mr-1"></i><?= $_SESSION['userData']['nombrerol']; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-key text-primary mr-2"></i>Usuario:</strong></td>
                                                <td><code><?= $_SESSION['userData']['usuario']; ?></code></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Información adicional según el rol -->
                            <?php if ($_SESSION['userData']['idrol'] == 5): // Docente ?>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <strong>Información Docente:</strong> Puede gestionar sus materias y horarios desde el panel docente.
                            </div>
                            <?php elseif ($_SESSION['userData']['idrol'] == 7): // Estudiante ?>
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-graduation-cap"></i>
                                <strong>Información Estudiantil:</strong> Revise sus pensiones y calificaciones en las pestañas correspondientes.
                            </div>
                            <?php elseif ($_SESSION['userData']['idrol'] == 1): // Administrador ?>
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-crown"></i>
                                <strong>Acceso Administrador:</strong> Tiene privilegios completos en el sistema.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Panel: Seguridad -->
                <div class="tab-pane fade" id="user-security">
                    <div class="tile">
                        <div class="tile-header">
                            <h4 class="line-head">
                                <i class="fas fa-shield-alt text-primary mr-2"></i>Cambiar contraseña
                            </h4>
                        </div>
                        <div class="tile-body">
                            <form id="formPerfil" name="formPerfil" class="needs-validation" novalidate>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Nota:</strong> Por seguridad, se recomienda cambiar su contraseña periódicamente.
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-id-card text-primary mr-2"></i>C.I.
                                            </label>
                                            <input class="form-control bg-light" type="text" 
                                                id="txtIdentificacion" name="txtIdentificacion" 
                                                value="<?= $_SESSION['userData']['ci']; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-phone text-primary mr-2"></i>Teléfono
                                            </label>
                                            <input class="form-control validNumber" type="text" 
                                                id="txtTelefono" name="txtTelefono" 
                                                value="<?= $_SESSION['userData']['cel']; ?>" 
                                                required maxlength="15">
                                            <small class="text-muted">Solo números, sin espacios ni guiones</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-user text-primary mr-2"></i>Nombre
                                            </label>
                                            <input class="form-control" type="text" 
                                                id="txtNombre" name="txtNombre" 
                                                value="<?= $_SESSION['userData']['nombre']; ?>" 
                                                required minlength="2">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-user text-primary mr-2"></i>Apellido
                                            </label>
                                            <input class="form-control" type="text" 
                                                id="txtApellido" name="txtApellido" 
                                                value="<?= $_SESSION['userData']['apellido']; ?>" 
                                                required minlength="2">
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-lock mr-2"></i>Cambiar contraseña
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-key mr-2"></i>Nueva contraseña
                                            </label>
                                            <div class="input-group">
                                                <input class="form-control" type="password" 
                                                    id="txtPassword" name="txtPassword" 
                                                    placeholder="Mínimo 6 caracteres" minlength="6">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" 
                                                            onclick="togglePassword('txtPassword', this)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Dejar en blanco para mantener la actual</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-check-circle mr-2"></i>Confirmar contraseña
                                            </label>
                                            <div class="input-group">
                                                <input class="form-control" type="password" 
                                                    id="txtPasswordConfirm" name="txtPasswordConfirm" 
                                                    placeholder="Repetir nueva contraseña" minlength="6">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" 
                                                            onclick="togglePassword('txtPasswordConfirm', this)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Indicador de fortaleza de contraseña -->
                                <div class="progress mb-3" style="height: 5px;" id="passwordStrengthBar">
                                    <div class="progress-bar" role="progressbar" style="width: 0%;"></div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary btn-lg" type="submit">
                                            <i class="fa fa-fw fa-lg fa-save"></i> Actualizar datos
                                        </button>
                                        <button class="btn btn-secondary btn-lg" type="reset">
                                            <i class="fa fa-fw fa-lg fa-undo"></i> Restablecer
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Panel: Configuración (Datos fiscales) -->
                <div class="tab-pane fade" id="user-settings">
                    <div class="tile">
                        <div class="tile-header">
                            <h4 class="line-head">
                                <i class="fas fa-file-invoice text-primary mr-2"></i>Datos fiscales
                            </h4>
                        </div>
                        <div class="tile-body">
                            <form id="formDataFiscal" name="formDataFiscal" class="needs-validation" novalidate>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Información:</strong> Estos datos serán utilizados para la generación de facturas.
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-barcode text-primary mr-2"></i>NIT
                                            </label>
                                            <input class="form-control" type="text" 
                                                id="txtNit" name="txtNit" 
                                                value="<?= $_SESSION['userData']['nit'] ?? ''; ?>" 
                                                placeholder="Ingrese su NIT" maxlength="20">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-user-tie text-primary mr-2"></i>Nombre fiscal
                                            </label>
                                            <input class="form-control" type="text" 
                                                id="txtNombreFiscal" name="txtNombreFiscal" 
                                                value="<?= $_SESSION['userData']['nombrefiscal'] ?? $_SESSION['userData']['nombre'].' '.$_SESSION['userData']['apellido']; ?>" 
                                                placeholder="Nombre o razón social">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-map-marked-alt text-primary mr-2"></i>Dirección fiscal
                                            </label>
                                            <textarea class="form-control" id="txtDirFiscal" 
                                                    name="txtDirFiscal" rows="2"
                                                    placeholder="Dirección completa para facturación"><?= $_SESSION['userData']['direccionfiscal'] ?? $_SESSION['userData']['direccion_dom']; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="custom-control custom-checkbox mb-3">
                                            <input type="checkbox" class="custom-control-input" 
                                                id="chkDatosFiscales" name="chkDatosFiscales">
                                            <label class="custom-control-label" for="chkDatosFiscales">
                                                Usar estos datos por defecto para todas las facturas
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fa fa-fw fa-lg fa-save"></i> Guardar datos fiscales
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>