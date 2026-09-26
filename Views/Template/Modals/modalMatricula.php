<div class="modal fade" id="modalFormMatricula" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Nueva Matricula</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="tile">
            <div class="tile-body">
            <form id="formNewMatricula" name="formNewMatricula" class="form-horizontal">
              <input type="hidden" id="newG" name="newG" value="1" required>
              <input type="hidden" id="idMatricula" name="idMatricula" value="0">
              <?= csrf_field(); ?>
              <p class="text-primary">Todos los campos son obligatorios, excepto folio.</p>

              <div class="form-row">
                <div class="form-group col-md-5">
                  <label for="txtCi">C.I.</label>
                  <input type="text" class="form-control" id="txtCi" name="txtCi" required>
                </div>
              </div>
              <div class="form-row">
                
              </div>           
              <div class="form-row">
                <div class="form-group col-md-3">
                  <label for="intGestion">Año lectivo</label>
                  <input type="text" class="form-control valid validNumber" id="intGestion" name="intGestion" required value="0" >
                </div>
                <div class="form-group col-md-5">
                    <label for="listParalelos">Curso</label>
                    <select class="form-control  " data-live-search="true" id="listParalelos" name="listParalelos" required >
                        
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="listTipoEstudiante">Tipo de Matricula</label>
                    <select class="form-control selectpicker" id="listTipoEstudiante" name="listTipoEstudiante" required >
                        <option value="Regular">Regular</option>
                        <option value="Becado">Becado</option>
                    </select>
                </div>
             </div>
             <div class="form-row">
                
                <div class="form-group col-md-12">
                    <label for="txtFolio">Folio</label>
                    <textarea class="form-control" id="txtFolio" name="txtFolio"rows="3"></textarea>
                </div>
                <div class="form-group col-md-6 centered">
                    <label for="listStateInscripcion">Confirmación de inscripción</label>
                    <select class="form-control selectpicker" id="listStateInscripcion" name="listStateInscripcion" required >
                        <option value="Confirmado">Confirmado</option>
                        <option value="Inscrito">Inscrito</option>
                        <option value="Pendiente_Documentos">Pendiente de documentos (30 días hábiles)</option>
                        <option value="Retirado">Retirado (conserva historial)</option>
                        <option value="Trasladado">Trasladado (conserva historial)</option>
                        <option value="Egresado">Egresado (conserva historial)</option>
                    </select>
                </div>
                <div class="form-group col-md-6" id="boxMotivoEstado" style="display:none;">
                    <label for="motivoEstado">Motivo del cambio *</label>
                    <input type="text" class="form-control" id="motivoEstado" name="motivoEstado" maxlength="255" placeholder="Ej. Traslado a UE San José, nota N°...">
                    <small class="form-text text-muted">Obligatorio en Retirado/Trasladado/Egresado.</small>
                </div>
                <div class="form-group col-md-12" id="boxMotivoRect" style="display:none;">
                    <label for="motivoRectificacion">Motivo de rectificación * <small class="text-muted">(solo Admin/Dirección, fuera de gestión activa)</small></label>
                    <input type="text" class="form-control" id="motivoRectificacion" name="motivoRectificacion" maxlength="255" placeholder="Ej. Registro extemporáneo 2024 autorizado por Dirección">
                </div>
              </div>
              <div class="alert alert-warning py-2">
                <div class="form-check mb-1">
                  <input type="checkbox" class="form-check-input" id="chkDocPendiente" name="chkDocPendiente" value="1">
                  <label class="form-check-label" for="chkDocPendiente"><strong>Documentación pendiente</strong> — no bloquea la inscripción.</label>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6 mb-1">
                    <label class="mb-1">Checklist entregado</label>
                    <div class="form-check"><input type="checkbox" class="form-check-input" name="doc_cert_nac" value="1"><label class="form-check-label">Cert. nacimiento</label></div>
                    <div class="form-check"><input type="checkbox" class="form-check-input" name="doc_rude" value="1"><label class="form-check-label">RUDE</label></div>
                    <div class="form-check"><input type="checkbox" class="form-check-input" name="doc_solicitud" value="1"><label class="form-check-label">Solicitud</label></div>
                  </div>
                  <div class="form-group col-md-6 mb-1">
                    <label for="plazoDocs">Plazo hasta (auto 30 días hábiles si vacío)</label>
                    <input type="date" class="form-control" id="plazoDocs" name="plazoDocs">
                    <div class="form-check mt-2"><input type="checkbox" class="form-check-input" id="chkCompromiso" name="chkCompromiso" value="1" checked><label class="form-check-label" for="chkCompromiso">Compromiso firmado</label></div>
                    <input type="text" class="form-control form-control-sm mt-2" name="docsObs" placeholder="Observación (opcional)">
                  </div>
                </div>
              </div>
             <div class="tile-footer">
                <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Matricular Estudiante</span></button>&nbsp;&nbsp;&nbsp;
                <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
              </div>
            </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

