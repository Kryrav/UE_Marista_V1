<div class="modal fade" id="modalFormPagoPension" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Pagar mensualidad</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="tile">
            <div class="tile-body">
            <form id="formPagoPension" name="formPagoPension" class="form-horizontal">
              <p class="text-primary">El monto debe ser exacto (no se aceptan pagos parciales).</p>
              <p class="text-primary">Datos del estudiante</p>
              <div class="form-row">
                
                <div class="form-group col-md-2">
                  <label for="txtMatricula">Matricula: </label>
                  <input type="text" class="form-control valid validText" id="txtMatricula" name="txtMatricula" required="" disabled>
                  <input type="hidden" class="form-control valid validText" id="intIdPension" name="intIdPension" required="" value="0">
                  <input type="hidden" class="form-control valid validText" id="intCiEstudiante" name="intCiEstudiante" required="" value="0">
                </div>
                <div class="form-group col-md-6">
                  <label for="txtEstudiante">Estudiante: </label>
                  <input type="text" class="form-control valid validText" id="txtEstudiante" name="txtEstudiante" required="" disabled>
                </div>
                <div class="form-group col-md-2">
                  <label for="Mes">Mes: </label>
                  <input type="text" class="form-control " id="Mes" name="Mes" required=""disabled>
                </div>
                <div class="form-group col-md-2">
                  <label for="txtGestion">Año Lectivo: </label>
                  <input type="number" class="form-control " id="txtGestion" name="txtGestion" required=""disabled>
                </div>
                
              </div>
              <p class="text-primary">Datos del pago</p>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="txtMonto">Monto exacto (Bs.): </label>
                  <input type="number" step="0.01" class="form-control " id="txtMonto" name="txtMonto" required="" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label for="listTipoPago">Tipo:</label>
                  <select class="form-control selectpicker" id="listTipoPago" name="listTipoPago" required >
                      <option value="Efectivo">Efectivo</option>
                      <option value="Transferencia">Transferencia</option>
                      <option value="Deposito">Deposito</option>
                      <option value="Qr">Qr</option>
                  </select>
                </div>
                <div class="form-group col-md-4">
                  <label for="txtCodigo">Código (opcional): </label>
                  <input type="text" class="form-control " id="txtCodigo" name="txtCodigo">
                </div>
              </div>
              <br>
              <p class="text-primary">Datos del aportante</p>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="txtNameAportante">Nombre:</label>
                  <input type="text" class="form-control " id="txtNameAportante" name="txtNameAportante" required="">
                </div>
                <div class="form-group col-md-4">
                  <label for="txtLastAportante">Apellido:</label>
                  <input type="text" class="form-control " id="txtLastAportante" name="txtLastAportante" required="">
                </div>
                <div class="form-group col-md-4">
                  <label for="txtCiAportante">CI:</label>
                  <input type="text" class="form-control " id="txtCiAportante" name="txtCiAportante" required="">
                </div>
                <div class="form-group col-md-4">
                  <label for="txtParentesco">Parentesco:</label>
                  <input type="text" class="form-control " id="txtParentesco" name="txtParentesco" required="">
                </div>
              </div>
             
              <div class="tile-footer">
                <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Pagar</span></button>&nbsp;&nbsp;&nbsp;
                <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancelar</button>
              </div>
            </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<!-- --------------------------------------------------------------------------- -->
<div class="modal fade" id="modalFormBuscarEstudiante" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Datos del Estudiante</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="container  align-items-center ">
        <div class="modal-body">
            <div class="tile ">
              <div class="tile-body">
              <form id="formCiEstudiante" name="formCiEstudiante" class="form-horizontal">
                <input type="hidden" id="idEstudiante" name="idEstudiante" value="">
                <p class="text-primary">Obligatorio.</p>

                <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="txtCiEstudiante">C.I.:</label>
                      <input type="text" class="form-control valid " id="txtCiEstudiante" name="txtCiEstudiante" required="">
                    </div>
                  </div>
                  <div class="form-group col-md-12">
                    <p class="text-primary"> Ingrese el CI del estudiante para consultar la información de mensualidades pagadas y pendientes.</p>
                    
                  </div>
                  
                </div>
                <div class="tile-footer">
                  <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Buscar</span></button>&nbsp;&nbsp;&nbsp;
                  <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
                </div>
              </form>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

