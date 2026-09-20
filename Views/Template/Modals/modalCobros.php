<div class="modal fade" id="modalFormCobros" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Nuevo Cobro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="tile">
            <div class="tile-body">
            <form id="formCobro" name="formCobro" class="form-horizontal">
              <input type="hidden" id="idCobro" name="idCobro" value="0">
              <p class="text-primary">Todos los campos son obligatorios, excepto descripción.</p>

              <div class="form-row">
                <div class="form-group col-md-5">
                  <label for="txtTituloCobro">Título: </label>
                  <input type="text" class="form-control valid validText" id="txtTituloCobro" name="txtTituloCobro" required="">
                </div>
                <div class="form-group col-md-4">
                  <label for="txtMonto">Monto (Bs.): </label>
                  <input type="number" step="0.01" min="0.01" class="form-control " id="txtMonto" name="txtMonto" required="">
                </div>
                <div class="form-group col-md-3">
                    <label for="listTipoPago">Tipo:</label>
                    <select class="form-control selectpicker" id="listTipoPago" name="listTipoPago" required >
                        <option value="Unico">Único</option>
                        <option value="Mensual">Mensual</option>
                    </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="txtDesPago">Descripción del Pago: </label>
                    <textarea class="form-control" id="txtDesPago" name="txtDesPago"rows="2"></textarea>
                </div>                
              </div>
           
              <div class="form-row">
                <div class="form-group col-md-3">
                  <label for="intCuota">N° cuotas:</label>
                  <input type="number" class="form-control" id="intCuota" name="intCuota" value="1" min="1">
                </div>
                
                <div class="form-group col-md-4">
                    <label for="statusCobro">Status Cobro:</label>
                    <select class="form-control selectpicker" id="statusCobro" name="status" required >
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
             </div>
             
              <div class="tile-footer">
                <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Guardar</span></button>&nbsp;&nbsp;&nbsp;
                <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
              </div>
            </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>




<!-- --------------------------------------------------------------------------- -->
<div class="modal fade" id="modalFormPagos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Pago</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="tile">
            <div class="tile-body">
            <form id="formMatricula" name="formMatricula" class="form-horizontal">
              <input type="hidden" id="idEstudiante" name="idEstudiante" value="">
              <p class="text-primary">Todos los campos son obligatorios.</p>

              <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="txtEstudiante">Seleccione estudiante: </label>
                    <select class="form-control selectpicker" id="txtEstudiante" name="txtEstudiante" required >
                        <option value="1">Rene Alejandro Vasquez</option>
                        <option value="2">Pedro Pascal Toledo</option>
                        <option value="3">Juan Coleman Toledo</option>
                        <option value="4">Bernardo Vasquez Lopez</option>
                        <option value="5">Yesica Pascal Torrez</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="TipoEstudiante">Seleccione Cobro: </label>
                    <select class="form-control selectpicker" id="TipoEstudiante" name="TipoEstudiante" required >
                        <option value="1">Cuota Refacción de Cancha</option>
                        <option value="2">Cuota Refacción de Teatro</option>
                        <option value="3">Cuota Cortinas</option>
                        <option value="4">Feria de Comida</option>
                        <option value="5">Uso de Sistema Web</option>
                    </select>
                </div>
              </div>
              <div class="form-row">
              <div class="form-group col-md-6">
                  <label for="txtCiPagare">C.I.:</label>
                  <input type="text" class="form-control valid validText" id="txtCiPagare" name="txtCiPagare" required="">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="txtNombrePagare">Nombre:</label>
                  <input type="text" class="form-control valid validText" id="txtNombrePagare" name="txtNombrePagare" required="">
                </div>
                <div class="form-group col-md-6">
                  <!-- Verifica los ID para JAva Script Usuario -->
                  <label for="txtApellidoPagare">Apellido:</label> 
                  <input type="text" class="form-control valid validText" id="txtApellidoPagare" name="txtApellidoPagare" required="">
                </div>
                <div class="form-group col-md-6">
                  <label for="txtCelularPagare">Celular:</label>
                  <input type="text" class="form-control valid validText" id="txtCelularPagare" name="txtCelularPagare" required="">
                </div>
                <div class="form-group col-md-6">
                  <!-- Verifica los ID para JAva Script Usuario -->
                  <label for="txtRelacPagare">Relación:</label> 
                  <input type="text" class="form-control valid validText" id="txtRelacPagare" name="txtRelacPagare" required="">
                </div>
                <div class="form-group col-md-6">
                    <label for="TipoPAgo">Tipo de pago: </label>
                    <select class="form-control selectpicker" id="TipoPAgo" name="TipoPAgo" required >
                        <option value="1">Efectivo</option>
                        <option value="2">Pago QR</option>
                        <option value="3">Depósito Bancario</option>
                        <option value="4">Transacción</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                  <!-- Verifica los ID para JAva Script Usuario -->
                  <label for="txtCodeTrans">Código de transacción:</label> 
                  <input type="text" class="form-control " id="txtCodeTrans" name="txtCodeTrans" >
                </div>
                
                <!-- Año lectivo  -->
                
              </div>
           
            
              <div class="tile-footer">
                <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Pagar</span></button>&nbsp;&nbsp;&nbsp;
                <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
              </div>
            </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

