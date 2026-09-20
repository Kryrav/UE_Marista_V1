<div class="modal fade" id="modalFormTutores" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModalTutor">Nuevo Tutor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="tile">
            <div class="tile-body">
            <form id="formTutor" name="formTutor" class="form-horizontal">
              <input type="hidden" id="idPadre" name="idPadre" value="0">
              <p class="text-primary">Datos del tutor / padre</p>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="txtCiTutor">C.I.</label>
                  <input type="text" class="form-control" id="txtCiTutor" name="txtCiTutor" required>
                  <small id="avisoTutorExiste" class="form-text text-info" style="display:none;">CI registrado como tutor: se prellenan sus datos y solo se vinculará.</small>
                </div>
                <div class="form-group col-md-4">
                  <label for="txtNombreTutor">Nombres</label>
                  <input type="text" class="form-control" id="txtNombreTutor" name="txtNombreTutor" required>
                </div>
                <div class="form-group col-md-4">
                  <label for="txtApellidoTutor">Apellidos</label>
                  <input type="text" class="form-control" id="txtApellidoTutor" name="txtApellidoTutor" required>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="listSexoTutor">Sexo</label>
                    <select class="form-control" id="listSexoTutor" name="listSexoTutor">
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                  <label for="txtCelTutor">Celular</label>
                  <input type="text" class="form-control" id="txtCelTutor" name="txtCelTutor" required>
                </div>
                <div class="form-group col-md-6">
                  <label for="txtEmailTutor">Email</label>
                  <input type="email" class="form-control" id="txtEmailTutor" name="txtEmailTutor" required>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="txtDireccionTutor">Domicilio</label>
                  <input type="text" class="form-control" id="txtDireccionTutor" name="txtDireccionTutor">
                  <div class="form-check mt-1">
                    <input type="checkbox" class="form-check-input" id="chkMismoDom" name="chkMismoDom" value="1">
                    <label class="form-check-label" for="chkMismoDom"><small>Mismo domicilio que el estudiante (copia su dirección exacta)</small></label>
                  </div>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="txtPasswordTutor">Password (vacío = CI)</label>
                  <input type="text" class="form-control" id="txtPasswordTutor" name="txtPasswordTutor">
                </div>
                <div class="form-group col-md-6">
                    <label for="listStatusTutor">Status</label>
                    <select class="form-control" id="listStatusTutor" name="listStatusTutor">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
              </div>
              <p class="text-primary">Vínculo con estudiante</p>
              <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="buscarEstudiante">Estudiante (busque por CI, RUDE o nombre)</label>
                    <input type="text" class="form-control" id="buscarEstudiante" placeholder="Escriba al menos 2 letras..." autocomplete="off">
                    <input type="hidden" id="listEstudiante" name="listEstudiante" value="">
                    <div id="resultEstudiante" class="tut-autocomplete" style="display:none;"></div>
                    <div id="chipEstudiante" class="mt-2"></div>
                    <div id="tutoresActuales" class="mt-2"></div>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="listParentesco">Parentesco</label>
                    <select class="form-control" id="listParentesco" name="listParentesco">
                        <option value="Padre">Padre</option>
                        <option value="Madre">Madre</option>
                        <option value="Tutor">Tutor</option>
                        <option value="Tío">Tío</option>
                        <option value="Tía">Tía</option>
                        <option value="Hermano">Hermano</option>
                        <option value="Apoderado">Apoderado</option>
                    </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="txtNacionalidad">Nacionalidad</label>
                  <input type="text" class="form-control" id="txtNacionalidad" name="txtNacionalidad" value="Boliviana">
                </div>
                <div class="form-group col-md-4">
                  <label for="listEstadoCivil">Estado civil</label>
                  <select class="form-control" id="listEstadoCivil" name="listEstadoCivil">
                      <option value="">--</option>
                      <option value="Soltero">Soltero</option>
                      <option value="Casado">Casado</option>
                      <option value="Divorciado">Divorciado</option>
                      <option value="Viudo">Viudo</option>
                  </select>
                </div>
                <div class="form-group col-md-4">
                  <label for="txtProfesion">Profesión</label>
                  <input type="text" class="form-control" id="txtProfesion" name="txtProfesion">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="txtEmpresa">Empresa de trabajo</label>
                  <input type="text" class="form-control" id="txtEmpresa" name="txtEmpresa">
                </div>
                <div class="form-group col-md-6">
                  <label for="txtObservaciones">Observaciones</label>
                  <input type="text" class="form-control" id="txtObservaciones" name="txtObservaciones">
                </div>
              </div>
              <div class="tile-footer">
                <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnTextTutor">Guardar</span></button>&nbsp;&nbsp;&nbsp;
                <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
              </div>
            </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
