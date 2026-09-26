let tableEstudiantes;
let rowTable = "";
let divLoading = document.querySelector("#divLoading");
let estStep = 1;
let paralelosCargados = false;

document.addEventListener('DOMContentLoaded', function(){
    tableEstudiantes = $('#tableEstudiantes').dataTable({
        // Procesado en cliente: el backend devuelve la lista completa y
        // DataTables pagina/filtra/ordena localmente (filtros y buscador funcionan).
        "processing": true,
        "serverSide": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": " " + base_url + "/Estudiantes/getEstudiantesAll",
            "dataSrc": ""
        },
        "columns": [
            {"data": "foto", "name": "foto", "orderable": false, "searchable": false},
            {"data": "ci", "name": "ci"},
            {"data": "legajo", "name": "legajo"},
            {"data": "nombre", "name": "nombre"},
            {"data": "apellido", "name": "apellido"},
            {"data": "curso_actual", "name": "curso_actual"},
            {"data": "tutores", "name": "tutores", "orderable": false},
            {"data": "email", "name": "email"},
            {"data": "cel", "name": "cel"},
            {"data": "status_estudiante", "name": "status_estudiante"},
            {"data": "options", "name": "options", "orderable": false, "searchable": false},
            // Columnas ocultas para filtros exactos (inmunes al HTML de las celdas)
            {"data": "curso_raw", "name": "curso_raw", "visible": false, "searchable": true},
            {"data": "estado_raw", "name": "estado_raw", "visible": false, "searchable": true},
            {"data": "legajo_flag", "name": "legajo_flag", "visible": false, "searchable": true}
        ],
        'dom': 'lBfrtip',
        'buttons': [
            {"extend": "copyHtml5", "text": "<i class='far fa-copy'></i> Copiar", "titleAttr": "Copiar", "className": "btn btn-secondary", "exportOptions": {"columns": [1, 2, 3, 4, 5, 7, 8]}},
            {"extend": "excelHtml5", "text": "<i class='fas fa-file-excel'></i> Excel", "titleAttr": "Esportar a Excel", "className": "btn btn-success", "exportOptions": {"columns": [1, 2, 3, 4, 5, 7, 8]}},
            {"extend": "pdfHtml5", "text": "<i class='fas fa-file-pdf'></i> PDF", "titleAttr": "Esportar a PDF", "className": "btn btn-danger", "exportOptions": {"columns": [1, 2, 3, 4, 5, 7, 8]}},
            {"extend": "csvHtml5", "text": "<i class='fas fa-file-csv'></i> CSV", "titleAttr": "Esportar a CSV", "className": "btn btn-info", "exportOptions": {"columns": [1, 2, 3, 4, 5, 7, 8]}}
        ],
        "resonsieve": "true",
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[2, "asc"]],
        "initComplete": function(){
            construirFiltroCursos();
            actualizarResumenEst();
        }
    });

    tableEstudiantes.on('draw.dt', function(){ actualizarResumenEst(); });
    // Reconstruye el filtro de cursos con TODOS los datos en cada recarga
    tableEstudiantes.on('xhr.dt', function(){ construirFiltroCursos(); });

    let fEst = document.querySelector('#filtroEstadoEst');
    if(fEst){
        fEst.onchange = function(){
            // 1=Activo, 2=Inactivo sobre la columna oculta (match exacto).
            // (Eliminados status=0 no los trae el backend: soft delete oculto)
            let api = tableEstudiantes.api();
            if(this.value === 'todos'){ api.column('estado_raw:name').search('').draw(); }
            else{ api.column('estado_raw:name').search('^' + this.value + '$', true, false).draw(); }
        };
    }
    let fCur = document.querySelector('#filtroCursoEst');
    if(fCur){
        fCur.onchange = function(){
            let api = tableEstudiantes.api();
            if(!this.value){ api.column('curso_raw:name').search('').draw(); return; }
            // Coincidencia exacta sobre el texto plano (columna oculta)
            let esc = this.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            api.column('curso_raw:name').search('^' + esc + '$', true, false).draw();
        };
    }
    let chkSin = document.querySelector('#chkSinFolio');
    if(chkSin){
        chkSin.onchange = function(){
            let api = tableEstudiantes.api();
            api.column('legajo_flag:name').search(this.checked ? '^0$' : '', true, false).draw();
        };
    }

    // Preview de foto
    let inpFoto = document.querySelector('#fotoEstudiante');
    if(inpFoto){
        inpFoto.onchange = function(){
            if(this.files && this.files[0]){
                let rd = new FileReader();
                rd.onload = function(e){ document.querySelector('#previewFoto').src = e.target.result; };
                rd.readAsDataURL(this.files[0]);
            }
        };
    }

    document.querySelector('#btnNextStep').onclick = function(){ wizardGo(estStep + 1); };
    document.querySelector('#btnPrevStep').onclick = function(){ wizardGo(estStep - 1); };

    if(document.querySelector("#formEstudiante")){
        let formEstudiante = document.querySelector("#formEstudiante");
        formEstudiante.onsubmit = function(e) {
            e.preventDefault();
            if(!validarPaso(1) || !validarPaso(2)){ wizardGo(1); swal("Atención", "Complete los campos obligatorios de los pasos 1 y 2.", "error"); return false; }
            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            request.open("POST", base_url + '/Estudiantes/setEstudiante', true);
            request.send(new FormData(formEstudiante));
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    divLoading.style.display = "none";
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        tableEstudiantes.api().ajax.reload(null, false);
                        rowTable = "";
                        $('#modalFormEstudiantes').modal("hide");
                        formEstudiante.reset();
                        swal("Estudiantes", objData.msg, "success");
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                }
                return false;
            }
        }
    }
}, false);

// ---------- Wizard ----------
// En edición solo hay 2 pasos (el 3 es de alta) y el botón Actualizar
// está visible siempre. La contraseña no se gestiona aquí (módulo Usuarios).
function esEdicion(){
    return document.querySelector('#newStudent').value == "0";
}

function wizardGo(n){
    let max = esEdicion() ? 2 : 3;
    if(n < 1) n = 1;
    if(n > max) n = max;
    if(n === 2 && !validarPaso(1)){ swal("Atención", "Complete los campos obligatorios del paso 1.", "error"); return false; }
    if(n === 3 && !validarPaso(2)){ swal("Atención", "Complete los datos del paso 2.", "error"); return false; }
    estStep = n;
    document.querySelectorAll('.est-step').forEach(function(s){ s.style.display = (parseInt(s.dataset.step) === n) ? '' : 'none'; });
    document.querySelectorAll('.est-steps li').forEach(function(li){ li.classList.toggle('active', parseInt(li.dataset.step) <= n); });
    document.querySelector('#btnPrevStep').style.display = n > 1 ? '' : 'none';
    document.querySelector('#btnNextStep').style.display = n < max ? '' : 'none';
    document.querySelector('#btnActionForm').style.display = (n === max || esEdicion()) ? '' : 'none';
    if(n === 3 && document.querySelector('#newStudent').value == "1" && !paralelosCargados){ cargarParalelosMat(); }
}

function validarPaso(n){
    let ok = true;
    document.querySelectorAll('.est-step[data-step="' + n + '"] [required]').forEach(function(el){
        if(el.type === 'email'){
            if(el.value.trim() === '' || el.value.indexOf('@') < 0){ ok = false; el.classList.add('is-invalid'); }
            else{ el.classList.remove('is-invalid'); }
        }else if(el.value.trim() === ''){ ok = false; el.classList.add('is-invalid'); }
        else{ el.classList.remove('is-invalid'); }
    });
    // ITERACIÓN 1: diferibles opcionales — solo formato si vienen con valor
    if(n === 1){
        let em = document.querySelector('#txtEmail');
        if(em && em.value.trim() !== '' && em.value.indexOf('@') < 0){ ok = false; em.classList.add('is-invalid'); }
        let ce = document.querySelector('#txtCelular');
        if(ce && ce.value.trim() !== ''){
            let d = ce.value.replace(/[^0-9]/g, '');
            if(d.length < 7 || d.length > 9){ ok = false; ce.classList.add('is-invalid'); }
            else{ ce.classList.remove('is-invalid'); }
        }
    }
    return ok;
}

function cargarParalelosMat(){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url + '/Estudiantes/paralelosMatricula', true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let o = JSON.parse(request.responseText);
            if(o.status){
                document.querySelector('#listParaleloMat').innerHTML = o.html;
                document.querySelector('#gestionMatLabel').innerHTML = '(gestión ' + o.gestion + ')';
                paralelosCargados = true;
            }
        }
    }
}

// ---------- Resumen y filtros ----------
function actualizarResumenEst(){
    let api = tableEstudiantes.api();
    let info = api.page.info();
    let el = document.querySelector('#resumenEst');
    if(el) el.innerHTML = 'Mostrando <b>' + info.recordsDisplay + '</b> de <b>' + info.recordsTotal + '</b> estudiantes';
}

function construirFiltroCursos(){
    let sel = document.querySelector('#filtroCursoEst');
    if(!sel) return;
    let keep = sel.value || '';
    sel.innerHTML = '<option value="">Todos los cursos</option>';
    let api = tableEstudiantes.api();
    let vals = {};
    api.rows().every(function(){
        let d = this.data();
        // Usa el texto plano (columna oculta): sin tags ni entidades
        let t = (d.curso_raw || '').trim();
        if(t !== ''){ vals[t] = true; }
    });
    Object.keys(vals).sort().forEach(function(c){
        let op = document.createElement('option');
        op.value = c; op.textContent = c;
        sel.appendChild(op);
    });
    if(keep && vals[keep]){ sel.value = keep; }
    else if(keep !== ''){ tableEstudiantes.api().column(4).search('', false, true); }
}

// ---------- Eliminar ----------
function fntDelEstudiante(idEstudiante){
    swal({
        title: "Dar de baja",
        text: "Se bloqueará su acceso y sus vínculos de tutor. Si tiene matrículas activas no se podrá eliminar.",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, dar de baja",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        if(isConfirm)
        {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            request.open("POST", base_url + '/Estudiantes/delEstudiante', true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send("idEstudiante=" + idEstudiante);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        swal("Baja", objData.msg, "success");
                        tableEstudiantes.api().ajax.reload(null, false);
                    }else{
                        swal("Atención", objData.msg, "error");
                    }
                }
            }
        }
    });
}

// ---------- Alta ----------
function openModal()
{
    let f = document.querySelector("#formEstudiante");
    f.reset();
    document.querySelector('#previewFoto').src = base_url + '/Assets/images/avatar.png';
    document.querySelector('#idEstudiante').value = "";
    document.querySelector('#newStudent').value = 1;
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Estudiante";
    document.querySelector('#boxMatricular').style.display = '';
    document.querySelector('#chkMatricular').checked = true;
    document.querySelector('.est-steps li[data-step="3"]').style.display = '';
    // ITERACIÓN 1: reset documental
    let cd = document.querySelector('#chkDocPendiente'); if(cd) cd.checked = false;
    ['doc_cert_nac','doc_rude','doc_solicitud'].forEach(function(n){
        let el = document.querySelector('#formEstudiante [name="'+n+'"]'); if(el) el.checked = false;
    });
    let cc = document.querySelector('#chkCompromiso'); if(cc) cc.checked = true;
    let ob = document.querySelector('#docsObs'); if(ob) ob.value = "";
    paralelosCargados = false;
    wizardGo(1);
    $('#modalFormEstudiantes').modal('show');
}

// ---------- Ficha 360° ----------
function fntViewEstudiante(idEstudiante){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url + '/Estudiantes/getEstudiante/' + idEstudiante, true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status && objData.ficha)
            {
                let d = objData.ficha.estudiante;
                fichaIdEstudiante = d.id_estudiante;
                let tieneFoto = objData.ficha.foto_url.indexOf('uploads/estudiantes/') !== -1;
                document.querySelector("#fichaFoto").src = objData.ficha.foto_url;
                document.querySelector("#fichaFotoNota").style.display = tieneFoto ? 'none' : '';
                document.querySelector("#fichaNombre").innerHTML = d.nombre + ' ' + d.apellido;
                document.querySelector("#fichaCi").innerHTML = 'CI ' + d.ci + ' · RUDE ' + d.rude;
                let estHtml = '<span class="badge badge-danger">Eliminado</span>';
                if(d.estudiante_status == 1){ estHtml = '<span class="badge badge-success">Activo</span>'; }
                else if(d.estudiante_status == 2){ estHtml = '<span class="badge badge-warning">Inactivo</span>'; }
                document.querySelector("#fichaStatus").innerHTML = estHtml;
                document.querySelector("#fRude").innerHTML = d.rude;
                document.querySelector("#fFnac").innerHTML = d.fnacimiento;
                document.querySelector("#fSexo").innerHTML = d.sexo;
                document.querySelector("#fColegio").innerHTML = d.colegio_proc || '—';
                document.querySelector("#fCiudad").innerHTML = d.ciudad || '—';
                document.querySelector("#fPais").innerHTML = d.pais || '—';
                document.querySelector("#fCel").innerHTML = d.cel;
                document.querySelector("#fEmail").innerHTML = d.email;
                document.querySelector("#fDom").innerHTML = d.direccion_dom || '—';
                document.querySelector("#fEmerg").innerHTML = d.emergencia || '—';

                let p = objData.ficha.pensiones;
                document.querySelector("#fichaKpis").innerHTML =
                    '<div class="col-4"><div class="ficha-kpi"><b>' + p.pagadas + '/' + p.total + '</b><small>Pensiones pagadas</small></div></div>'
                    + '<div class="col-4"><div class="ficha-kpi"><b>Bs. ' + p.deuda + '</b><small>Deuda pendiente</small></div></div>'
                    + '<div class="col-4"><div class="ficha-kpi"><b>' + objData.ficha.tutores.length + '</b><small>Tutores</small></div></div>';

                let ht = '';
                if(objData.ficha.tutores.length === 0){ ht = '<p class="text-muted mb-0">Sin tutores registrados.</p>'; }
                objData.ficha.tutores.forEach(function(t){
                    ht += '<div class="ficha-tutor"><i class="fa fa-user"></i><span><b>' + t.nombre + ' ' + t.apellido + '</b> (' + t.tipo_parentesco + ') · ' + t.cel + '</span></div>';
                });
                document.querySelector("#fichaTutores").innerHTML = ht;

                let hm = '';
                if(objData.ficha.matriculas.length === 0){ hm = '<p class="text-muted mb-0">Sin matrículas.</p>'; }
                objData.ficha.matriculas.forEach(function(m){
                    hm += '<div class="ficha-mat"><i class="fa fa-id-card-o"></i><span><b>' + m.gestion + '</b> · ' + (m.curso || 'Sin curso') + ' · ' + m.tipo + ' · ' + m.estado_inscripcion + '</span></div>';
                });
                document.querySelector("#fichaMatriculas").innerHTML = hm;

                document.querySelector("#fichaPensiones").innerHTML = htmlPensionesFicha(objData.ficha);

                // Tira de legajo físico
                let folio = d.folio_fisico || '';
                let ubic = [d.estante ? ('Est. ' + d.estante) : '', d.gaveta ? ('Gav. ' + d.gaveta) : ''].filter(Boolean).join(' · ');
                let estLeg = {'archivado': 'secondary', 'prestado': 'warning', 'digitalizado': 'info', 'observado': 'danger'};
                let legHtml;
                if(folio !== ''){
                    legHtml = '<div class="legajo-num"><small>FOLIO</small>' + String(folio).padStart(6, '0') + '</div>'
                        + '<div class="legajo-ubic"><i class="fa fa-archive"></i>' + (ubic !== '' ? escHtml(ubic) : 'Ubicación sin registrar') + '</div>'
                        + (d.estado_legajo && estLeg[d.estado_legajo]
                            ? '<span class="badge badge-' + estLeg[d.estado_legajo] + '">' + escHtml(d.estado_legajo) + '</span>'
                            : '<span class="badge badge-light">sin estado</span>');
                }else{
                    legHtml = '<div class="legajo-num"><small>FOLIO</small>—</div>'
                        + '<div class="legajo-ubic text-muted">Sin folio asignado (se autogenera al guardar cambios)</div>';
                }
                document.querySelector("#legajoStrip").innerHTML = legHtml;

                document.querySelector("#cntTutores").innerHTML = objData.ficha.tutores.length;
                // Vuelve siempre a la pestaña Datos al abrir
                let firstTab = document.querySelector('#fichaTabs .nav-link');
                if(window.jQuery && firstTab){ window.jQuery(firstTab).tab('show'); }
                $('#modalFichaEstudiante').modal('show');
            }else{
                swal("Error", objData.msg, "error");
            }
        }
    }
}

// ---------- Edición ----------
function fntEditEstudiante(element, idEstudiante){
    rowTable = "";
    document.querySelector('#newStudent').value = 0;
    document.querySelector('#titleModal').innerHTML = "Actualizar Estudiante";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url + '/Estudiantes/getEstudiante/' + idEstudiante, true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status)
            {
                let d = objData.ficha ? objData.ficha.estudiante : objData.data;
                document.querySelector("#idEstudiante").value = d.id_estudiante;
                document.querySelector("#txtCi").value = d.ci;
                document.querySelector("#txtRUDE").value = d.rude;
                document.querySelector("#txtNombre").value = d.nombre;
                document.querySelector("#txtApellido").value = d.apellido;
                document.querySelector("#txtCelular").value = d.cel;
                document.querySelector("#txtEmail").value = d.email;
                document.querySelector("#txtDireccion").value = d.direccion_dom;
                document.querySelector("#listEst").value = d.estado_reg;
                document.querySelector("#txtColegioProc").value = d.colegio_proc;
                document.querySelector("#listSexEst").value = d.sexo;
                document.querySelector("#dateFNacimiento").value = d.fnacimiento;
                document.querySelector("#txtPais").value = d.pais;
                document.querySelector("#txtCiudad").value = d.ciudad;
                document.querySelector("#txtProvincia").value = d.provincia;
                document.querySelector("#txtEmergencia").value = d.emergencia;
                document.querySelector("#previewFoto").src = (objData.ficha && objData.ficha.foto_url) ? objData.ficha.foto_url : (base_url + '/Assets/images/avatar.png');
                document.querySelector("#listStatus").value = (d.estudiante_status == 2) ? 2 : 1;
                document.querySelector("#txtFolio").value = d.folio_fisico || "";
                document.querySelector("#txtEstante").value = d.estante || "";
                document.querySelector("#txtGaveta").value = d.gaveta || "";
                document.querySelector("#listEstadoLeg").value = d.estado_legajo || "";
                document.querySelector('#boxMatricular').style.display = 'none';
                // En edición el paso 3 no existe: se oculta su indicador
                document.querySelector('.est-steps li[data-step="3"]').style.display = 'none';
                wizardGo(1);
                $('#modalFormEstudiantes').modal('show');
            }else{
                swal("Error", objData.msg, "error");
            }
        }
    }
}

// ---------- Lightbox de foto ----------
document.addEventListener('click', function(e){
    let wrap = e.target.closest ? e.target.closest('#fichaFotoWrap') : null;
    if(wrap){
        document.querySelector('#fotoGrande').src = document.querySelector('#fichaFoto').src;
        document.querySelector('#fotoLightbox').style.display = 'flex';
        return;
    }
    if(e.target.closest && e.target.closest('#fotoLightbox')){
        document.querySelector('#fotoLightbox').style.display = 'none';
    }
});
document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
        let lb = document.querySelector('#fotoLightbox');
        if(lb) lb.style.display = 'none';
    }
});

// ---------- Tutores ----------
let fichaIdEstudiante = 0;

function fntCarnetEstudiante(){
    if(!fichaIdEstudiante){ swal("Error", "Abra primero la ficha del estudiante.", "error"); return; }
    window.open(base_url + '/Estudiantes/carnet/' + fichaIdEstudiante, '_blank');
}
function fntImprimirHistorial(idMatricula){
    if(!idMatricula){ swal("Error", "Matrícula inválida.", "error"); return; }
    window.open(base_url + '/Estudiantes/historial/' + idMatricula, '_blank');
}

function escHtml(s){
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function inicialesTutor(nombre, apellido){
    return ((nombre || '').trim().charAt(0) + (apellido || '').trim().charAt(0)).toUpperCase() || '?';
}

// Detalle de pensiones ordenado por gestión (reciente primero) y mes calendario.
// Pendientes resaltados primero dentro de cada gestión, con subtotales y totales.
function htmlPensionesFicha(ficha){
    let det = ficha.pensiones_detalle || [];
    let p = ficha.pensiones || {total: 0, pagadas: 0, pendientes: 0, cobrado: 0, deuda: 0};
    let html = '<div class="pen-totales">'
        + '<span>Total: <b>' + p.total + '</b></span>'
        + '<span class="text-success">Pagado: <b>Bs. ' + p.cobrado + ' (' + p.pagadas + ')</b></span>'
        + '<span class="text-danger">Adeudado: <b>Bs. ' + p.deuda + ' (' + p.pendientes + ')</b></span>'
        + '</div>';
    if(det.length === 0){ return html + '<p class="text-muted mb-0">Sin pensiones registradas.</p>'; }
    let gestiones = {};
    det.forEach(function(r){
        (gestiones[r.gestion] = gestiones[r.gestion] || []).push(r);
    });
    Object.keys(gestiones).sort(function(a, b){ return b - a; }).forEach(function(g){
        let rows = gestiones[g].slice().sort(function(a, b){ return a.estado_pago - b.estado_pago; });
        let pag = 0, pen = 0;
        let body = '';
        let idMat = rows.length > 0 ? rows[0].id_matricula : 0;
        rows.forEach(function(r){
            let pagada = (r.estado_pago == 1);
            let vencida = (!pagada && r.vencida == 1);
            if(pagada){ pag += parseFloat(r.monto); } else { pen += parseFloat(r.monto); }
            body += '<tr class="' + (pagada ? '' : (vencida ? 'pen-vencida' : 'pen-pendiente')) + '">'
                + '<td>' + escHtml(r.mes) + '</td>'
                + '<td class="text-right">Bs. ' + r.monto + '</td>'
                + '<td>' + (pagada ? '<span class="badge badge-success">Pagado</span>'
                    : (vencida ? '<span class="badge badge-danger">Vencido</span><br><small class="text-muted">venció ' + escHtml(r.fecha_vencimiento || '') + '</small>'
                    : '<span class="badge badge-warning">Pendiente</span>')) + '</td>'
                + '<td><small>' + escHtml(r.tipo_pago || (pagada ? '' : '—')) + '</small></td>'
                + '<td><small>' + escHtml(r.fecha_reg_pago || '—') + '</small></td>'
                + '</tr>';
        });
        html += '<h6 class="pen-gestion">Gestión ' + g + ' <small class="text-muted">· ' + (gestiones[g][0].curso || '') + '</small>'
            + ' <button class="btn btn-outline-secondary btn-sm ml-2" onclick="fntImprimirHistorial(' + idMat + ')" title="Imprimir historial de esta matrícula"><i class="fa fa-print"></i> Historial</button></h6>'
            + '<div class="table-responsive"><table class="table table-sm table-bordered pen-tabla">'
            + '<thead><tr><th>Mes</th><th class="text-right">Monto</th><th>Estado</th><th>Tipo pago</th><th>Fecha pago</th></tr></thead>'
            + '<tbody>' + body + '</tbody>'
            + '<tfoot><tr><th colspan="1">Subtotal</th><th class="text-right">Bs. ' + pen.toFixed(2) + ' adeud.</th>'
            + '<th colspan="3" class="text-success">Bs. ' + pag.toFixed(2) + ' pagado</th></tr></tfoot>'
            + '</table></div>';
    });
    return html;
}

function fntTutoresEstudiante(idEstudiante){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url + '/Tutores/getByEstudiante/' + idEstudiante, true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let o = JSON.parse(request.responseText);
            if(o.status && o.data && o.data.length > 0){
                let html = '';
                o.data.forEach(function(t){
                    let extras = '';
                    if(t.profesion) extras += '<div class="tut-row"><i class="fa fa-briefcase"></i><span>' + escHtml(t.profesion) + (t.empresa_trabajo ? ' · ' + escHtml(t.empresa_trabajo) : '') + '</span></div>';
                    let meta = [t.nacionalidad, t.estado_civil].filter(Boolean).map(escHtml).join(' · ');
                    if(meta) extras += '<div class="tut-row"><i class="fa fa-id-card-o"></i><span>' + meta + '</span></div>';
                    if(t.observaciones) extras += '<div class="tut-row"><i class="fa fa-sticky-note-o"></i><span>' + escHtml(t.observaciones) + '</span></div>';
                    html += '<div class="tut-card">'
                        + '<div class="tut-avatar">' + escHtml(inicialesTutor(t.nombre, t.apellido)) + '</div>'
                        + '<div class="tut-body">'
                        + '<div class="tut-nombre">' + escHtml(t.nombre) + ' ' + escHtml(t.apellido) + ' <span class="badge badge-warning">' + escHtml(t.tipo_parentesco) + '</span></div>'
                        + '<div class="tut-row"><i class="fa fa-id-card"></i><span>CI ' + escHtml(t.ci) + '</span></div>'
                        + '<div class="tut-contacto">'
                        + '<a class="btn btn-success btn-sm" href="tel:' + escHtml((t.cel || '').replace(/[^0-9+]/g, '')) + '" title="Llamar"><i class="fa fa-phone"></i> ' + escHtml(t.cel || '—') + '</a> '
                        + '<a class="btn btn-info btn-sm" href="mailto:' + escHtml(t.email || '') + '" title="Escribir correo"><i class="fa fa-envelope"></i> ' + escHtml(t.email || '—') + '</a>'
                        + '</div>'
                        + '<div class="tut-row"><i class="fa fa-home"></i><span>' + escHtml(t.direccion_dom || 'Sin dirección registrada') + '</span></div>'
                        + extras
                        + '</div></div>';
                });
                document.querySelector('#listaTutoresEst').innerHTML = html;
                $('#modalTutoresEstudiante').modal('show');
            }else{
                swal("Tutores", "Este estudiante aún no tiene tutor registrado.", "warning");
            }
        }
    }
}
