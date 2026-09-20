let tablePensionesPagadas;
let tableMorosidad;
let rowTable = "";
let divLoading = document.querySelector("#divLoading");
let chartEstadoObj = null;
let chartMesesObj = null;
const MEN_COLORS = { cobrado: '#27ae60', vigente: '#f39c12', vencido: '#e74c3c', barra: '#1a3b5d', barra2: '#c4a35a' };

document.addEventListener('DOMContentLoaded', function(){
    cargarResumen();

    tablePensionesPagadas = $('#tablePensionesPagadas').dataTable({
        "processing": true, "serverSide": false,
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" },
        "ajax": { "url": " " + base_url + "/Pensiones/listPensionesPagadas", "dataSrc": "" },
        "columns": [
            {"data": "nro_recibo"},
            {"data": "CI_Estudiante"},
            {"data": "Matricula"},
            {"data": "Nombre_Estudiante"},
            {"data": "Apellido_Estudiante"},
            {"data": "Curso"},
            {"data": "Gestion"},
            {"data": "mes_pago"},
            {"data": "cajero"},
            {"data": "Estado_Pago"},
            {"data": "options", "orderable": false, "searchable": false}
        ],
        'dom': 'lBfrtip',
        'buttons': [
            {"extend": "copyHtml5", "text": "<i class='far fa-copy'></i> Copiar", "titleAttr": "Copiar", "className": "btn btn-secondary"},
            {"extend": "excelHtml5", "text": "<i class='fas fa-file-excel'></i> Excel", "titleAttr": "Exportar a Excel", "className": "btn btn-success"},
            {"extend": "pdfHtml5", "text": "<i class='fas fa-file-pdf'></i> PDF", "titleAttr": "Exportar a PDF", "className": "btn btn-danger"},
            {"extend": "csvHtml5", "text": "<i class='fas fa-file-csv'></i> CSV", "titleAttr": "Exportar a CSV", "className": "btn btn-info"}
        ],
        "bDestroy": true, "iDisplayLength": 10, "order": [[0, "desc"]]
    });

    tableMorosidad = $('#tableMorosidad').dataTable({
        "processing": true, "serverSide": false,
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" },
        "ajax": { "url": " " + base_url + "/Pensiones/morosidad", "dataSrc": "" },
        "columns": [
            {"data": "ci_estudiante"},
            {"data": "estudiante"},
            {"data": "curso"},
            {"data": "gestion"},
            {"data": "mes"},
            {"data": "fecha_vencimiento"},
            {"data": "monto"},
            {"data": "estado"},
            {"data": "options", "orderable": false, "searchable": false}
        ],
        'dom': 'lBfrtip',
        'buttons': [
            {"extend": "excelHtml5", "text": "<i class='fas fa-file-excel'></i> Excel", "titleAttr": "Exportar a Excel", "className": "btn btn-success"},
            {"extend": "pdfHtml5", "text": "<i class='fas fa-file-pdf'></i> PDF", "titleAttr": "Exportar a PDF", "className": "btn btn-danger"}
        ],
        "bDestroy": true, "iDisplayLength": 10, "order": [[5, "asc"]]
    });

    // Consultar mensualidades de estudiante por CI
    if(document.querySelector("#formCiEstudiante")){
        let formCiEstudiante = document.querySelector("#formCiEstudiante");
        formCiEstudiante.onsubmit = function(e) {
            e.preventDefault();
            let Ci = document.querySelector('#txtCiEstudiante').value;
            if(Ci.trim() == ''){
                swal("Atención", "Ingrese el CI del estudiante.", "error");
                return false;
            }
            buscarPensionesCiEstudiante(Ci);
        }
    }

    // Pagar mensualidad (monto exacto validado en servidor)
    if(document.querySelector("#formPagoPension")){
        let formPago = document.querySelector("#formPagoPension");
        formPago.onsubmit = function(e) {
            e.preventDefault();
            let Nombre = document.querySelector('#txtNameAportante').value;
            let Apellido = document.querySelector('#txtLastAportante').value;
            let Ci = document.querySelector('#txtCiAportante').value;
            let Parentesco = document.querySelector('#txtParentesco').value;
            let IdPension = document.querySelector('#intIdPension').value;
            if(Nombre.trim()==''||Apellido.trim()==''||Parentesco.trim()==''||Ci.trim()==''||IdPension=='0'){
                swal("Atención", "Complete los datos del aportante.", "error");
                return false;
            }
            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            request.open("POST", base_url + '/Pensiones/setPagoPension', true);
            request.send(new FormData(formPago));
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    divLoading.style.display = "none";
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        let ciEst = document.querySelector('#intCiEstudiante').value;
                        $('#modalFormPagoPension').modal("hide");
                        formPago.reset();
                        swal({title: "Mensualidad pagada", text: objData.msg, type: "success", showCancelButton: true, confirmButtonText: "Imprimir recibo", cancelButtonText: "Cerrar", closeOnConfirm: false},
                        function(isConfirm){
                            if(isConfirm){ fntImprimirRecibo(IdPension); }
                            refrescarMensualidades(ciEst);
                        });
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                }
                return false;
            }
        }
    }
}, false);

function refrescarMensualidades(ci){
    if(tablePensionesPagadas) tablePensionesPagadas.api().ajax.reload(null, false);
    if(tableMorosidad) tableMorosidad.api().ajax.reload(null, false);
    cargarResumen();
    if(ci){ buscarPensionesCiEstudiante(ci, true); }
}

function cargarResumen(){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url + '/Pensiones/resumen', true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let o = JSON.parse(request.responseText);
            if(o.status){
                let d = o.data;
                document.querySelector('#kpiGestionLbl').innerHTML = d.gestion_activa || '—';
                document.querySelector('#kpiGestionLbl2').innerHTML = d.gestion_activa || '—';
                document.querySelector('#kpiCobrado').innerHTML = 'Bs. ' + d.cobrado_gestion;
                document.querySelector('#kpiAdeudado').innerHTML = 'Bs. ' + d.adeudado_gestion;
                document.querySelector('#kpiVencido').innerHTML = 'Bs. ' + d.vencido_bs;
                document.querySelector('#kpiVencidas').innerHTML = d.vencidas + ' cuota(s) vencida(s)';
                document.querySelector('#kpiHist').innerHTML = 'Bs. ' + d.cobrado;
                document.querySelector('#badgeMora').innerHTML = d.vencidas;
                dibujarDona(d);
            }
        }
    }
    // Serie mensual para el gráfico de barras
    let rs = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    rs.open("GET", base_url + '/Pensiones/serie', true);
    rs.send();
    rs.onreadystatechange = function(){
        if(rs.readyState == 4 && rs.status == 200){
            let o = JSON.parse(rs.responseText);
            if(o.status){ dibujarBarras(o.data); }
        }
    }
}

function dibujarDona(d){
    if(typeof Chart === 'undefined') return;
    let cv = document.querySelector('#chartEstado');
    if(!cv) return;
    document.querySelector('#chartDonaSub').innerHTML = 'gestión ' + (d.gestion_activa || '—');
    document.querySelector('#legendEstado').innerHTML =
        '<span><i style="background:' + MEN_COLORS.cobrado + '"></i>Cobrado</span>'
        + '<span><i style="background:' + MEN_COLORS.vigente + '"></i>Adeudado vigente</span>'
        + '<span><i style="background:' + MEN_COLORS.vencido + '"></i>Vencido</span>';
    if(chartEstadoObj){ chartEstadoObj.destroy(); }
    // Dona coherente por gestión: cobrado + adeudado vigente + vencido (misma gestión)
    let venG = parseFloat(d.vencido_gestion || 0);
    let vigente = Math.max(0, parseFloat(d.adeudado_gestion) - venG);
    chartEstadoObj = new Chart(cv, {
        type: 'doughnut',
        data: { labels: ['Cobrado', 'Adeudado vigente', 'Vencido'],
            datasets: [{ data: [parseFloat(d.cobrado_gestion), vigente, venG],
                backgroundColor: [MEN_COLORS.cobrado, MEN_COLORS.vigente, MEN_COLORS.vencido], borderWidth: 2, borderColor: '#fff' }] },
        options: { maintainAspectRatio: false,
            plugins: { legend: { display: false },
                tooltip: { callbacks: { label: function(c){ return ' ' + c.label + ': Bs. ' + c.parsed; } } } },
            cutout: '62%' }
    });
}

function dibujarBarras(s){
    if(typeof Chart === 'undefined') return;
    let cv = document.querySelector('#chartMeses');
    if(!cv) return;
    document.querySelector('#chartBarrasSub').innerHTML = 'gestión ' + (s.gestion || '—') + ' · Bs. cobrados vs adeudado por mes';
    if(chartMesesObj){ chartMesesObj.destroy(); }
    chartMesesObj = new Chart(cv, {
        type: 'bar',
        data: { labels: s.labels,
            datasets: [
                { label: 'Cobrado', data: s.cobrado, backgroundColor: MEN_COLORS.barra, borderRadius: 4 },
                { label: 'Adeudado', data: s.adeudado, backgroundColor: MEN_COLORS.barra2, borderRadius: 4 }
            ] },
        options: { maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } },
            scales: { y: { beginAtZero: true, title: { display: true, text: 'Bs.' } } } }
    });
}

// Botones de cabecera
function openModal()
{
    fntConsultarEstudiante();
}

function openModalPago()
{
    fntConsultarEstudiante();
}

function fntConsultarEstudiante()
{
    let f = document.querySelector("#formCiEstudiante");
    if(f) f.reset();
    $('#modalFormBuscarEstudiante').modal('show');
}

function buscarPensionesCiEstudiante(Ci, silencioso)
{
    if(Ci.trim() == ''){
        swal("Atención", "Ingrese el CI del estudiante.", "error");
        return false;
    }
    divLoading.style.display = "flex";
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let formData = new FormData();
    formData.append("txtCiEstudiante", Ci);
    request.open("POST", base_url + '/Pensiones/getPensionesEst/', true);
    request.send(formData);
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            divLoading.style.display = "none";
            let objData = JSON.parse(request.responseText);
            if(objData.status)
            {
                document.querySelector('#celCiEstudiante').innerHTML = objData.ci;
                document.querySelector('#celNombreEstudiante').innerHTML = objData.nombre;
                document.querySelector('#listaPensionesEstudiante').innerHTML = objData.tabla;
                $('#modalFormBuscarEstudiante').modal("hide");
                if(!silencioso){ swal("Encontrado", objData.msg, "success"); }
            }else{
                swal("Error", objData.msg, "error");
            }
        }
        return false;
    }
}

function fntPagarPension(idPension)
{
    document.querySelector("#formPagoPension").reset();
    divLoading.style.display = "flex";
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url + '/Pensiones/getDatoIdPension/' + idPension, true);
    request.send();
    request.onreadystatechange = function(){
        divLoading.style.display = "none";
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status)
            {
                document.querySelector("#intIdPension").value = objData.data.id_pension;
                document.querySelector("#txtMatricula").value = objData.data.id_matricula;
                document.querySelector("#txtEstudiante").value = objData.data.nombre_estudiante + ' ' + objData.data.apellido_estudiante;
                document.querySelector("#Mes").value = objData.data.mes_pension;
                document.querySelector("#txtGestion").value = objData.data.gestion;
                document.querySelector("#txtMonto").value = objData.data.monto_pagar;
                document.querySelector("#intCiEstudiante").value = objData.data.ci_estudiante;
                $('#modalFormPagoPension').modal('show');
            }else{
                swal("Error", objData.msg || "Sin datos.", "error");
            }
        }
        return false;
    }
}

// Ver = abrir recibo (las cobradas ya están pagadas)
function fntViewPension(idPension){ fntImprimirRecibo(idPension); }
function fntEditPension(element, idPension){ fntImprimirRecibo(idPension); }
function fntDelPension(idPension){ fntAnularPension(idPension); }

function fntAnularPension(idPension)
{
    swal({
        title: "Anular pago",
        text: "Indique el motivo de la anulación (queda auditado):",
        type: "input",
        showCancelButton: true,
        confirmButtonText: "Anular",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false,
        inputPlaceholder: "Ej. cobro duplicado, error de mes..."
    }, function(motivo){
        if(motivo === false) return false;
        if(!motivo || motivo.trim() === ''){ swal.showInputError("El motivo es obligatorio."); return false; }
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let formData = new FormData();
        formData.append('idPension', idPension);
        formData.append('motivo', motivo.trim());
        request.open("POST", base_url + '/Pensiones/anularPension', true);
        request.send(formData);
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                let objData = JSON.parse(request.responseText);
                if(objData.status){
                    swal("Anulado", objData.msg, "success");
                    let ci = document.querySelector('#celCiEstudiante');
                    refrescarMensualidades(ci && ci.innerHTML !== '*' ? ci.innerHTML : null);
                }else{
                    swal("Error", objData.msg, "error");
                }
            }
        }
    });
}

function fntImprimirRecibo(idPension)
{
    const width = 800;
    const height = 650;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;
    window.open(
      base_url + '/Pensiones/recibos/' + idPension,
      "_blank",
      "width=" + width + ",height=" + height + ",top=" + top + ",left=" + left + ",resizable=yes,scrollbars=yes"
    );
}
