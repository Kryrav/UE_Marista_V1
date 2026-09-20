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
                    </select>
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

