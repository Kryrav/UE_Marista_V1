<!-- Modal para formulario de usuario -->
<div class="modal fade" id="modalFormUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">
                    <i class="fas fa-user"></i> Nuevo Usuario
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formUsuario" name="formUsuario" class="form-horizontal needs-validation" novalidate>
                    <input type="hidden" id="idUsuario" name="idUsuario" value="">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Todos los campos marcados con <span class="text-danger">*</span> son obligatorios.
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="txtCi">C.I. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control validNumber" id="txtCi" name="txtCi" 
                                   placeholder="Ej: 1234567" required maxlength="15">
                            <div class="invalid-feedback">El CI es obligatorio</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtCel">Nº Celular <span class="text-danger">*</span></label>
                            <input type="text" class="form-control validNumber" id="txtCel" name="txtCel" 
                                   placeholder="Ej: 71234567" required maxlength="15">
                            <div class="invalid-feedback">El celular es obligatorio</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="txtNombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control validText" id="txtNombre" name="txtNombre" 
                                   placeholder="Ej: Juan Carlos" required>
                            <div class="invalid-feedback">El nombre es obligatorio</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtApellido">Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control validText" id="txtApellido" name="txtApellido" 
                                   placeholder="Ej: Pérez García" required>
                            <div class="invalid-feedback">El apellido es obligatorio</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="txtSexo">Sexo <span class="text-danger">*</span></label>
                            <select class="form-control selectpicker" id="txtSexo" name="txtSexo" required>
                                <option value="">Seleccione...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            <div class="invalid-feedback">El sexo es obligatorio</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtEmail">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="txtEmail" name="txtEmail" 
                                   placeholder="Ej: usuario@correo.com" required>
                            <div class="invalid-feedback">El email es obligatorio y debe ser válido</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="txtDireccion">Domicilio <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="txtDireccion" name="txtDireccion" 
                                  rows="2" placeholder="Dirección completa" required></textarea>
                        <div class="invalid-feedback">La dirección es obligatoria</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="listRolid">Tipo de usuario <span class="text-danger">*</span></label>
                            <select class="form-control selectpicker" data-live-search="true" 
                                    id="listRolid" name="listRolid" required>
                                <option value="">Seleccione...</option>
                                <!-- Se carga vía AJAX -->
                            </select>
                            <div class="invalid-feedback">El tipo de usuario es obligatorio</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="listStatus">Estado <span class="text-danger">*</span></label>
                            <select class="form-control selectpicker" id="listStatus" name="listStatus" required>
                                <option value="">Seleccione...</option>
                                <option value="1">Activo</option>
                                <option value="2">Inactivo</option>
                            </select>
                            <small class="form-text text-muted">Inactivo: visible pero sin acceso. La baja (eliminado) se hace con el botón <i class="fas fa-trash-alt"></i> de la tabla.</small>
                            <div class="invalid-feedback">El estado es obligatorio</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="txtUserAcces">Usuario de acceso</label>
                            <input type="text" class="form-control" id="txtUserAcces" name="txtUserAcces" 
                                   placeholder="Dejar vacío para usar el email">
                            <small class="form-text text-muted">Si se deja vacío, se usará el email</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtPassword">Contraseña <span class="text-danger" id="passwordRequired">*</span></label>
                            <input type="password" class="form-control" id="txtPassword" name="txtPassword" 
                                   placeholder="Mínimo 6 caracteres" minlength="6">
                            <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres</div>
                        </div>
                    </div>

                    <div class="tile-footer bg-light p-3 mt-3 rounded">
                        <div class="row">
                            <div class="col-md-6">
                                <button id="btnActionForm" class="btn btn-primary btn-block" type="submit">
                                    <i class="fa fa-save"></i> <span id="btnText">Guardar</span>
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-danger btn-block" type="button" data-dismiss="modal">
                                    <i class="fa fa-times"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver usuario -->
<div class="modal fade" id="modalViewUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-circle"></i> Datos del usuario
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th width="40%"><i class="fas fa-id-card"></i> C.I.:</th>
                                <td id="celCi">-</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-user"></i> Nombres:</th>
                                <td id="celNombre">-</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-user"></i> Apellidos:</th>
                                <td id="celApellido">-</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-venus-mars"></i> Sexo:</th>
                                <td id="celSexo">-</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-phone"></i> Celular:</th>
                                <td id="celCel">-</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-map-marker-alt"></i> Domicilio:</th>
                                <td id="celDireccion">-</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-envelope"></i> Correo:</th>
                                <td id="celEmail">-</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-user-tag"></i> Tipo:</th>
                                <td id="celTipoUsuario">-</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-key"></i> Usuario:</th>
                                <td id="celUserAcces">-</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-circle"></i> Estado:</th>
                                <td id="celEstado">-</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-calendar-alt"></i> Registro:</th>
                                <td id="celFechaRegistro">-</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PERFIL -->
 <!-- Modal para editar perfil -->
<div class="modal fade" id="modalFormPerfil" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit"></i> Editar Perfil
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formPerfil" name="formPerfil" class="form-horizontal">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Complete los campos para actualizar su información personal.
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="txtIdentificacion">C.I.</label>
                            <input type="text" class="form-control" id="txtIdentificacion" name="txtIdentificacion" 
                                   value="<?= $_SESSION['userData']['ci']; ?>" readonly>
                            <small class="form-text text-muted">No se puede modificar</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtTelefono">Teléfono</label>
                            <input type="text" class="form-control validNumber" id="txtTelefono" name="txtTelefono" 
                                   value="<?= $_SESSION['userData']['cel']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="txtNombre">Nombre</label>
                            <input type="text" class="form-control" id="txtNombre" name="txtNombre" 
                                   value="<?= $_SESSION['userData']['nombre']; ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtApellido">Apellido</label>
                            <input type="text" class="form-control" id="txtApellido" name="txtApellido" 
                                   value="<?= $_SESSION['userData']['apellido']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="txtPassword">Nueva contraseña</label>
                            <input type="password" class="form-control" id="txtPassword" name="txtPassword" 
                                   placeholder="Mínimo 6 caracteres" minlength="6">
                            <small class="form-text text-muted">Dejar en blanco para no cambiar</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtPasswordConfirm">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="txtPasswordConfirm" name="txtPasswordConfirm" 
                                   placeholder="Repetir contraseña">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="col-md-12 text-right">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-save"></i> Actualizar datos
                            </button>
                            <button class="btn btn-danger" type="button" data-dismiss="modal">
                                <i class="fa fa-times"></i> Cancelar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>