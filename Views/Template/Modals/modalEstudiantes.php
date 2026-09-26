<!-- Modal alta/edición por pasos -->
<div class="modal fade" id="modalFormEstudiantes" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" >
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Nuevo Estudiante</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form id="formEstudiante" name="formEstudiante" class="form-horizontal" enctype="multipart/form-data">
              <input type="hidden" id="idEstudiante" name="idEstudiante" value="">
              <input type="hidden" id="newStudent" name="newStudent" value="1">
              <ul class="est-steps">
                <li class="active" data-step="1"><span>1</span> Personales</li>
                <li data-step="2"><span>2</span> Académicos</li>
                <li data-step="3"><span>3</span> Acceso y matrícula</li>
              </ul>

              <!-- PASO 1 -->
              <div class="est-step" data-step="1">
                <div class="text-center mb-3">
                  <img id="previewFoto" src="<?= media(); ?>/images/avatar.png" class="est-preview" alt="foto">
                  <div><label for="fotoEstudiante" class="btn btn-outline-secondary btn-sm mt-2 mb-0"><i class="fa fa-camera"></i> Foto (JPG/PNG, máx 2 MB)</label></div>
                  <input type="file" id="fotoEstudiante" name="fotoEstudiante" accept="image/jpeg,image/png,image/webp" style="display:none;">
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="txtCi">C.I. *</label>
                    <input type="text" class="form-control" id="txtCi" name="txtCi" required="">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="txtRUDE">RUDE <small class="text-muted">(diferible 30 días hábiles)</small></label>
                    <input type="text" class="form-control" id="txtRUDE" name="txtRUDE" placeholder="Se puede completar después">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="txtNombre">Nombres *</label>
                    <input type="text" class="form-control" id="txtNombre" name="txtNombre" required="">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="txtApellido">Apellidos *</label>
                    <input type="text" class="form-control" id="txtApellido" name="txtApellido" required="">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-4">
                      <label for="listSexEst">Sexo *</label>
                      <select class="form-control" id="listSexEst" name="listSexEst" required >
                          <option value="M">Masculino</option>
                          <option value="F">Femenino</option>
                      </select>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="dateFNacimiento">Fecha de nacimiento *</label>
                    <input type="date" class="form-control" id="dateFNacimiento" name="dateFNacimiento" required="">
                  </div>
                  <div class="form-group col-md-4">
                    <label for="txtCelular">Nº Celular <small class="text-muted">(diferible)</small></label>
                    <input type="text" class="form-control" id="txtCelular" name="txtCelular" placeholder="7-9 dígitos, si se conoce">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="txtEmail">Email <small class="text-muted">(diferible)</small></label>
                    <input type="email" class="form-control" id="txtEmail" name="txtEmail" placeholder="Se puede completar después">
                  </div>
                  <div class="form-group col-md-6">
                      <label for="txtDireccion">Domicilio</label>
                      <input type="text" class="form-control" id="txtDireccion" name="txtDireccion">
                  </div>
                </div>
              </div>

              <!-- PASO 2 -->
              <div class="est-step" data-step="2" style="display:none;">
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="listEst">Estudiante</label>
                    <select class="form-control" id="listEst" name="listEst" required >
                        <option value="Nuevo">Nuevo</option>
                        <option value="Antiguo">Antiguo</option>
                        <option value="Retirado">Retirado</option>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="txtColegioProc">Colegio de procedencia</label>
                    <input type="text" class="form-control" id="txtColegioProc" name="txtColegioProc">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="txtPais">País</label>
                    <input type="text" class="form-control" id="txtPais" name="txtPais" value="Bolivia">
                  </div>
                  <div class="form-group col-md-4">
                      <label for="txtCiudad">Ciudad</label>
                      <input type="text" class="form-control" id="txtCiudad" name="txtCiudad">
                  </div>
                  <div class="form-group col-md-4">
                    <label for="txtProvincia">Provincia</label>
                    <input type="text" class="form-control" id="txtProvincia" name="txtProvincia">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-12">
                    <label for="txtEmergencia">En caso de emergencia (contacto)</label>
                    <textarea class="form-control" id="txtEmergencia" name="txtEmergencia" rows="2"></textarea>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="listStatus">Estado</label>
                      <select class="form-control" id="listStatus" name="listStatus" required >
                          <option value="1">Activo</option>
                          <option value="2">Inactivo</option>
                      </select>
                      <small class="form-text text-muted">Inactivo: visible pero sin matrícula. La baja (eliminado) se hace con el botón <i class="fa fa-trash-alt"></i> de la tabla.</small>
                  </div>
                </div>
                <p class="text-primary">Archivo físico / legajo</p>
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="txtFolio">Folio N° (único)</label>
                    <input type="number" class="form-control" id="txtFolio" name="txtFolio" min="1" placeholder="Auto si vacío">
                  </div>
                  <div class="form-group col-md-3">
                    <label for="txtEstante">Estante</label>
                    <input type="text" class="form-control" id="txtEstante" name="txtEstante" maxlength="10" placeholder="B">
                  </div>
                  <div class="form-group col-md-3">
                    <label for="txtGaveta">Gaveta</label>
                    <input type="text" class="form-control" id="txtGaveta" name="txtGaveta" maxlength="20" placeholder="2">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="listEstadoLeg">Estado</label>
                    <select class="form-control" id="listEstadoLeg" name="listEstadoLeg">
                        <option value="">—</option>
                        <option value="archivado">Archivado</option>
                        <option value="prestado">Prestado</option>
                        <option value="digitalizado">Digitalizado</option>
                        <option value="observado">Observado</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- PASO 3 -->
              <div class="est-step" data-step="3" style="display:none;">
                <div class="alert alert-info py-2">
                  <i class="fa fa-lock"></i> La contraseña se gestiona únicamente en el módulo <strong>Usuarios</strong>. Al crear, el acceso inicial es el C.I. del estudiante.
                </div>
                <div id="boxMatricular">
                  <hr>
                  <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input" id="chkMatricular" name="chkMatricular" value="1" checked>
                    <label class="form-check-label" for="chkMatricular"><strong>Matricular de una vez</strong> <span class="text-muted" id="gestionMatLabel"></span></label>
                  </div>
                  <div class="alert alert-warning py-2">
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="chkDocPendiente" name="chkDocPendiente" value="1">
                      <label class="form-check-label" for="chkDocPendiente"><strong>Documentación pendiente</strong> — se otorga plazo de 30 días hábiles (inscripción sin bloqueo, norma Bolivia).</label>
                    </div>
                    <div class="form-row mt-2">
                      <div class="form-group col-md-4 mb-1">
                        <div class="form-check"><input type="checkbox" class="form-check-input" id="doc_cert_nac" name="doc_cert_nac" value="1"><label class="form-check-label" for="doc_cert_nac">Cert. nacimiento</label></div>
                        <div class="form-check"><input type="checkbox" class="form-check-input" id="doc_rude" name="doc_rude" value="1"><label class="form-check-label" for="doc_rude">RUDE</label></div>
                        <div class="form-check"><input type="checkbox" class="form-check-input" id="doc_solicitud" name="doc_solicitud" value="1"><label class="form-check-label" for="doc_solicitud">Solicitud</label></div>
                      </div>
                      <div class="form-group col-md-8 mb-1">
                        <div class="form-check"><input type="checkbox" class="form-check-input" id="chkCompromiso" name="chkCompromiso" value="1" checked><label class="form-check-label" for="chkCompromiso">Compromiso firmado por apoderado</label></div>
                        <input type="text" class="form-control form-control-sm mt-1" id="docsObs" name="docsObs" placeholder="Observación documental (opcional)">
                        <small class="form-text text-muted">Si RUDE/email/celular están vacíos, se marca pendiente automáticamente.</small>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="listParaleloMat">Paralelo</label>
                        <select class="form-control" id="listParaleloMat" name="listParaleloMat"></select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="listTipoMat">Tipo de matrícula</label>
                        <select class="form-control" id="listTipoMat" name="listTipoMat">
                            <option value="Regular">Regular</option>
                            <option value="Becado">Becado</option>
                        </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="tile-footer d-flex justify-content-between">
                <button id="btnPrevStep" class="btn btn-secondary" type="button" style="display:none;"><i class="fa fa-arrow-left"></i> Atrás</button>
                <span></span>
                <span>
                  <button id="btnNextStep" class="btn btn-primary" type="button">Siguiente <i class="fa fa-arrow-right"></i></button>
                  <button id="btnActionForm" class="btn btn-primary" type="submit" style="display:none;"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Guardar</span></button>&nbsp;
                  <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
                </span>
              </div>
            </form>
      </div>
    </div>
  </div>
</div>

<!-- Ficha 360° del estudiante -->
<div class="modal fade" id="modalFichaEstudiante" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header ficha-header">
        <div class="ficha-foto-wrap" id="fichaFotoWrap" title="Clic para ampliar">
          <img id="fichaFoto" src="<?= media(); ?>/images/avatar.png" class="ficha-foto" alt="foto">
          <span class="ficha-foto-zoom"><i class="fa fa-search-plus"></i></span>
        </div>
        <div class="ficha-id">
          <h5 class="modal-title" id="fichaNombre">—</h5>
          <div><span class="badge badge-light" id="fichaCi">—</span> <span id="fichaStatus"></span></div>
          <small id="fichaFotoNota" class="ficha-foto-nota" style="display:none;">Sin foto registrada</small>
        </div>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row text-center mb-3" id="fichaKpis"></div>
        <div class="legajo-strip" id="legajoStrip"></div>
        <ul class="nav nav-tabs ficha-tabs" id="fichaTabs" role="tablist">
          <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabDatos" role="tab">Datos</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabTutores" role="tab">Tutores <span class="badge badge-warning" id="cntTutores">0</span></a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabMatriculas" role="tab">Matrículas</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabPensiones" role="tab">Pensiones</a></li>
        </ul>
        <div class="tab-content pt-3">
          <div class="tab-pane fade show active" id="tabDatos" role="tabpanel">
            <table class="table table-sm table-bordered">
              <tbody>
                <tr><td><strong>RUDE</strong></td><td id="fRude">—</td><td><strong>Fecha nac.</strong></td><td id="fFnac">—</td></tr>
                <tr><td><strong>Sexo</strong></td><td id="fSexo">—</td><td><strong>Colegio proc.</strong></td><td id="fColegio">—</td></tr>
                <tr><td><strong>Ciudad</strong></td><td id="fCiudad">—</td><td><strong>País</strong></td><td id="fPais">—</td></tr>
                <tr><td><strong>Celular</strong></td><td id="fCel">—</td><td><strong>Email</strong></td><td id="fEmail">—</td></tr>
                <tr><td><strong>Domicilio</strong></td><td id="fDom" colspan="3">—</td></tr>
                <tr><td><strong>Emergencia</strong></td><td id="fEmerg" colspan="3">—</td></tr>
              </tbody>
            </table>
          </div>
          <div class="tab-pane fade" id="tabTutores" role="tabpanel">
            <div id="fichaTutores"></div>
          </div>
          <div class="tab-pane fade" id="tabMatriculas" role="tabpanel">
            <div id="fichaMatriculas"></div>
          </div>
          <div class="tab-pane fade" id="tabPensiones" role="tabpanel">
            <div id="fichaPensiones"></div>
            <a href="<?= base_url(); ?>/Pensiones" class="btn btn-info btn-sm mt-2"><i class="fa fa-money"></i> Ir a pensiones</a>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn-sm" id="btnCarnet" onclick="fntCarnetEstudiante()"><i class="fa fa-id-card-o"></i> Carnet QR</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Tutores del estudiante: vista completa + contacto -->
<div class="modal fade" id="modalTutoresEstudiante" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header tut-header">
        <h5 class="modal-title"><i class="fa fa-users"></i> Tutores del estudiante</h5>
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

<!-- Lightbox de foto (overlay propio, sin apilar modales Bootstrap) -->
<div id="fotoLightbox" style="display:none;">
  <span id="fotoLightboxClose">&times;</span>
  <img id="fotoGrande" src="" alt="foto del estudiante">
</div>
