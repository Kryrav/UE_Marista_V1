
<!-- --------------------------------------------------------------------------- -->
<div class="modal fade" id="modalFormMateria" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Nueva Materia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="tile">
            <div class="tile-body">
            <form id="formMateria" name="formMateria" class="form-horizontal">

              <input type="hidden" id="idMateria" name="idMateria" value="">
              <!-- <p class="text-primary">Todos los campos son obligatorios.</p> -->

              <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="txtArea">Área: </label>
                    <input class="form-control" id="txtArea" name="txtArea" type="text" placeholder="Área de la materia" required="">
                </div>
                <div class="form-group col-md-6">
                    <label for="txtCampo">Materia: </label>
                    <input class="form-control" id="txtCampo" name="txtCampo" type="text" placeholder="Campo de la materia" required="">
                    
                </div>
               
              </div>
              <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="txtDescripcion">Descripción de Materia: </label>
                        <textarea class="form-control" id="txtDescripcion" name="txtDescripcion"rows="2"></textarea>
                    </div>                
              </div>    
              <hr>
              <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="listNivel">Nivel: </label>
                    <select class="form-control selectpicker" id="listNivel" name="listNivel" required >
                        <option value="Primaria">Primaria</option>
                        <option value="Secundaria">Secundaria</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="listGrado">Grado: </label>
                    <select class="form-control selectpicker" id="listGrado" name="listGrado" required >
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="Status">Status: </label>
                    <select class="form-control selectpicker" id="Status" name="Status" required >
                        <option value="1">Activa</option>
                        <option value="2">Inactiva</option>
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label for="horas">Horas: </label>
                    <select class="form-control selectpicker" id="horas" name="horas" required >
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                    </select>
                </div>
              </div>
              <div class="tile-footer">
                <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Registrar</span></button>&nbsp;&nbsp;&nbsp;
                <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
              </div>
            </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<!-- --------------------------------------------------------------------------------------------------------------------------------------- -->



<div class="modal fade" id="modalViewMateria" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" >
            
        <div class="modal-content">
            <div class="modal-header header-primary">
                <h5 class="modal-title" id="titleModal">Datos de Materia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                <tbody>
                    <tr>
                    <td>ID:</td>
                    <td id="celidMateria"></td>
                    </tr>
                    <tr>
                        <td>Área:</td>
                        <td id="celtxtArea"></td>
                    </tr>
                    <tr>
                        <td>Materia:</td>
                        <td id="celtxtCampo"></td>
                    </tr>
                    <tr>
                        <td>Descripción de Materia:</td>
                        <td id="celtxtDescripcion"></td>
                    </tr>
                    <tr>
                        <td>Nivel Estudio:</td>
                        <td id="cellistNivel"></td>
                    </tr>
                    <tr>
                        <td>Grado:</td>
                        <td id="cellistGrado"></td>
                    </tr>
                    <tr>
                        <td>Horas de materia:</td>
                        <td id="celhoras"></td>
                    </tr>
                    <tr>
                        <td>Fecha de registro:</td>
                        <td id="celfechaRegistro"></td>
                    </tr>
                
                    <tr>
                        <td>Estado:</td>
                        <td id="celStatus"><span class="badge badge-success"></span></td>
                    </tr>
                   
                </tbody>
                </table>
            </div>
            <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

