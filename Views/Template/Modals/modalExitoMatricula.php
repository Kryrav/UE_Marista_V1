<!-- Éxito accionable post-matrícula (FLUJO-ÓPTIMO 5): siguientes pasos en 1 clic -->
<div class="modal fade" id="modalExitoMatricula" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#28a745;color:#fff;">
        <h5 class="modal-title"><i class="fa fa-check-circle"></i> Matrícula registrada</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="exitoMatMsg" class="mb-3"></p>
        <div class="list-group" id="exitoMatLinks">
          <a href="#" id="exitoBtnComprobante" target="_blank" class="list-group-item list-group-item-action"><i class="fa fa-print"></i> Imprimir comprobante</a>
          <a href="#" id="exitoBtnPensiones" class="list-group-item list-group-item-action"><i class="fa fa-money"></i> Ver pensiones</a>
          <a href="#" id="exitoBtnTutor" class="list-group-item list-group-item-action"><i class="fa fa-users"></i> Vincular tutor</a>
          <a href="#" id="exitoBtnOtro" class="list-group-item list-group-item-action"><i class="fa fa-plus-circle"></i> Inscribir otro estudiante</a>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
// Muestra el éxito accionable. Requiere base_url global.
function fntExitoMatricula(o){
  document.querySelector('#exitoMatMsg').textContent = o.msg || 'Matrícula registrada.';
  document.querySelector('#exitoBtnComprobante').href = o.idMat ? (base_url + '/Matricula/comprobante/' + o.idMat) : '#';
  document.querySelector('#exitoBtnComprobante').style.display = o.idMat ? '' : 'none';
  document.querySelector('#exitoBtnPensiones').href = base_url + '/Pensiones';
  let bt = document.querySelector('#exitoBtnTutor');
  bt.onclick = function(e){
    e.preventDefault();
    try { if(o.idEst){ sessionStorage.setItem('tut_id', o.idEst); } } catch(x){}
    window.location.href = base_url + '/Tutores';
    return false;
  };
  let bo = document.querySelector('#exitoBtnOtro');
  bo.onclick = function(e){
    e.preventDefault();
    $('#modalExitoMatricula').modal('hide');
    if(typeof openModal === 'function'){ openModal(); }
    else { window.location.href = base_url + '/Estudiantes'; }
    return false;
  };
  $('#modalExitoMatricula').modal('show');
}
</script>
