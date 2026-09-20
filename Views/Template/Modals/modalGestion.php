<!-- --------------------------------------------------------------------------- -->
<div class="modal fade" id="modalAbrirGestion" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal"> Nueva Gestión</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="tile">
            <div class="tile-body">
                <form id="formGestion" name="formGestion" class="form-horizontal">
                    
                    <div class="form-row">
                        <input type="hidden" name="newG" id="newG" value=1>
                        <div class="mb-2 col-md-4">
                            <label for="anio" class="form-label">Año</label>
                            <input type="number" class="form-control" id="anio" name="anio" required>
                        </div>  
                        <div class="mb-2 col-md-4">
                            <label for="fechaInicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" required>
                        </div>
                        <div class="mb-2 col-md-4">
                            <label for="fechaFin" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" id="fechaFin" name="fechaFin" required>
                        </div>                  
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                                <label for="txtDescripcion">Descripción / Nota: </label>
                                <textarea class="form-control" id="txtDescripcion" name="txtDescripcion"rows="2"></textarea>
                        </div>
                    </div>
                                
                    <div class="form-row">
                        <div class="mb-2 col-md-6">
                            <label for="intPension" class="form-label">Valor de Mensualidad (Bs.):</label>
                            <input type="number" class="form-control" id="intPension" name="intPension" required>
                        </div>
                                     
                    </div> 
                    <div class="tile-footer">
                        <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Abrir Gestion</span></button>&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
                    </div>  
                    
                    
                </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>


<!-- Modal para ingreso de datos -->
