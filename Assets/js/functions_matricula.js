let tableMatricula;
let rowTable = "";
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){
   
    tableMaterias = $('#tableMatricula').dataTable( {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Matricula/getMatriculaAll",
            "dataSrc":""
        },
        "columns":[
            {"data":"gestion"},
            {"data":"id_matricula"},
            {"data":"ci_estudiante"},
            {"data":"nombre_estudiante"},
            {"data":"apellido_estudiante"},
            {"data":"curso"},
            {"data":"estado_inscripcion"},
            {"data":"estado_matricula"},
            {"data":"options"}
        ],
        'dom': 'lBfrtip',
        'buttons': [
            {
                "extend": "copyHtml5",
                "text": "<i class='far fa-copy'></i> Copiar",
                "titleAttr":"Copiar",
                "className": "btn btn-secondary"
            },{
                "extend": "excelHtml5",
                "text": "<i class='fas fa-file-excel'></i> Excel",
                "titleAttr":"Esportar a Excel",
                "className": "btn btn-success"
            },{
                "extend": "pdfHtml5",
                "text": "<i class='fas fa-file-pdf'></i> PDF",
                "titleAttr":"Esportar a PDF",
                "className": "btn btn-danger"
            },{
                "extend": "csvHtml5",
                "text": "<i class='fas fa-file-csv'></i> CSV",
                "titleAttr":"Esportar a CSV",
                "className": "btn btn-info"
            }
        ],
        "resonsieve":"true",
        "bDestroy": true,
        "iDisplayLength": 10,
        "order":[[0,"desc"]]  
    });

    // Sección nueva matrícula
    
    if(document.querySelector("#formNewMatricula")){

        let formMatricula = document.querySelector("#formNewMatricula");
        formMatricula.onsubmit = function(e) {
            e.preventDefault();
            //Valores de los campos del formulario (folio es texto libre, no se valida como número)
            let strCi = document.querySelector('#txtCi').value.trim();
            let boolNuevo = document.querySelector('#newG').value;
            let intGestion = document.querySelector('#intGestion').value.trim();
            let listParalelos = document.querySelector('#listParalelos').value;
            let listTipoEstudiante = document.querySelector('#listTipoEstudiante').value; // Regular Becado
            let listStateInscripcion= document.querySelector('#listStateInscripcion').value; //Inscrito o Confirmado

            // En edición no se exige CI (no se puede cambiar de estudiante)
            if(boolNuevo == "1" && strCi == ''){
                swal("Atención", "El CI del estudiante es obligatorio para matricular." , "error");
                return false;
            }
             //Validamos si ingresa los campos necesarios
            if(intGestion == '' || listParalelos == '' || listTipoEstudiante == '' || listStateInscripcion == '')
                {
                    swal("Atención", "Todos los campos son obligatorios en el formulario." , "error");
                    return false;
                }
            if(parseInt(intGestion) < 2000 || parseInt(intGestion) > 2100){
                swal("Atención", "Gestión inválida: "+intGestion , "error");
                return false;
            }
            // FLUJO-ÓPTIMO (2): en creación/rematriculación, revisar antes de confirmar
            if(boolNuevo == "1" && !window._revConfirmed){
                fntPreviewMatricula();
                return false;
            }
            window._revConfirmed = false;
            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            // ITERACIÓN 2: modo rematricular usa endpoint dedicado (sin folio/docs)
            if(window._rematMode){
                let fd = new FormData();
                fd.append('ci', document.querySelector('#txtCi').value.trim());
                fd.append('gestion', document.querySelector('#intGestion').value.trim());
                fd.append('paralelo', document.querySelector('#listParalelos').value);
                fd.append('tipo', document.querySelector('#listTipoEstudiante').value);
                let mr = document.querySelector('#motivoRectificacion');
                if(mr){ fd.append('motivoRectificacion', mr.value.trim()); }
                let tk0 = document.querySelector('#formNewMatricula input[name="csrf_token"]');
                if(tk0){ fd.append('csrf_token', tk0.value); }
                request.open("POST", base_url+'/Matricula/rematricular/', true);
                request.send(fd);
            }else{
                let ajaxUrl = base_url+'/Matricula/insertNewMatricula/';
                let formData = new FormData(formMatricula);
                request.open("POST",ajaxUrl,true);
                request.send(formData);
            }
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        tableMaterias.api().ajax.reload();
                        rowTable = "";
                        $('#modalFormMatricula').modal("hide");
                        formMatricula.reset();
                        document.querySelector('#newG').value = "1";
                        document.querySelector('#idMatricula').value = "0";
                        // FLUJO-ÓPTIMO (5): éxito accionable en vez de aviso plano
                        if(typeof fntExitoMatricula === 'function'){
                            fntExitoMatricula({msg: objData.msg, idMat: objData.idMatricula || 0, idEst: objData.idEstudiante || 0});
                        }else{
                            swal("Matrícula", objData.msg ,"success");
                        }
                    }else{
                        swal("Error", objData.msg , "error");
                    }
                }
                divLoading.style.display = "none";
            }

        }
    }
    // Confirmar tras revisar
    if(document.querySelector('#btnConfirmarMat')){
        document.querySelector('#btnConfirmarMat').onclick = function(){
            document.querySelector('#boxRevision').style.display = 'none';
            window._revConfirmed = true;
            document.querySelector("#formNewMatricula").requestSubmit();
        };
        document.querySelector('#btnCorregirMat').onclick = function(){
            document.querySelector('#boxRevision').style.display = 'none';
        };
    }
    // Autocomplete CI -> Estudiantes/buscar (FLUJO-ÓPTIMO 1)
    let ciMat = document.querySelector('#modalFormMatricula #txtCi');
    if(ciMat){
        ciMat.addEventListener('input', function(){
            window._revConfirmed = false;
            let box = document.querySelector('#boxRevision'); if(box) box.style.display = 'none';
            let v = this.value.trim();
            let res = document.querySelector('#resultCiMat');
            if(document.querySelector('#newG').value != "1" || this.disabled || v.length < 2){ if(res) res.style.display = 'none'; return; }
            let rq = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            rq.open("GET", base_url + '/Estudiantes/buscar?q=' + encodeURIComponent(v), true);
            rq.send();
            rq.onreadystatechange = function(){
                if(rq.readyState == 4 && rq.status == 200){
                    try {
                        let o = JSON.parse(rq.responseText);
                        if(o.status && o.data.length > 0){
                            let h = '';
                            o.data.slice(0, 8).forEach(function(s){
                                h += '<button type="button" class="list-group-item list-group-item-action" data-ci="' + s.ci + '">' + s.ci + ' · ' + s.nombre + ' ' + s.apellido + ' <small class="text-muted">' + (s.curso || '') + '</small></button>';
                            });
                            res.innerHTML = h; res.style.display = '';
                            res.querySelectorAll('button').forEach(function(b){
                                b.onclick = function(){ ciMat.value = b.dataset.ci; res.style.display = 'none'; };
                            });
                        }else{ res.style.display = 'none'; }
                    } catch(e){ res.style.display = 'none'; }
                }
            }
        });
        document.addEventListener('click', function(e){
            let res = document.querySelector('#resultCiMat');
            if(res && !e.target.closest('#resultCiMat') && e.target.id !== 'txtCi'){ res.style.display = 'none'; }
        });
    }
}, false);

// FLUJO-ÓPTIMO (2): resumen de confirmación sin escribir
function fntPreviewMatricula(){
    let ci = document.querySelector('#txtCi').value.trim();
    let g = document.querySelector('#intGestion').value.trim();
    let p = document.querySelector('#listParalelos').value;
    let t = document.querySelector('#listTipoEstudiante').value;
    let e = document.querySelector('#listStateInscripcion').value;
    let box = document.querySelector('#boxRevision');
    let body = document.querySelector('#revisionBody');
    body.innerHTML = 'Consultando...'; box.style.display = '';
    let rq = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    rq.open("GET", base_url + '/Matricula/preview?ci=' + encodeURIComponent(ci) + '&gestion=' + encodeURIComponent(g) + '&paralelo=' + encodeURIComponent(p) + '&tipo=' + encodeURIComponent(t) + '&estado=' + encodeURIComponent(e), true);
    rq.send();
    rq.onreadystatechange = function(){
        if(rq.readyState == 4 && rq.status == 200){
            try {
                let o = JSON.parse(rq.responseText);
                if(!o.status){ body.innerHTML = '<span class="text-danger">' + o.msg + '</span>'; return; }
                let d = o.data;
                let nom = d.estudiante ? (d.estudiante.nombre + ' ' + d.estudiante.apellido + ' (' + d.estudiante.ci + ')') : ('CI ' + ci + ' (nuevo: complete el alta en Estudiantes)');
                let cur = d.paralelo ? (d.paralelo.nivel + ' ' + d.paralelo.grado + ' "' + d.paralelo.sigla + '" · ' + d.paralelo.turno) : '—';
                let h = '<div><b>Estudiante:</b> ' + nom + '</div>'
                    + '<div><b>Curso:</b> ' + cur + ' · <b>Gestión:</b> ' + d.gestion + ' · <b>Tipo:</b> ' + d.tipo + ' · <b>Estado:</b> ' + d.estado + '</div>'
                    + '<div><b>Pensiones:</b> ' + d.cuotas + ' × Bs. ' + d.monto + ' = <b>Bs. ' + d.total + '</b></div>';
                if(d.warnings.length > 0){
                    h += '<div class="mt-1">' + d.warnings.map(function(w){ return '<span class="badge badge-warning mr-1">' + w + '</span>'; }).join('') + '</div>';
                }
                if(d.duplicado){ h += '<div class="text-danger mt-1">No se podrá guardar: duplicado.</div>'; }
                body.innerHTML = h;
            } catch(err){ body.innerHTML = '<span class="text-danger">No se pudo previsualizar.</span>'; }
        }
    }
}

// FLUJO-ÓPTIMO (4): la creación va por el wizard único de Estudiantes
function fntIrWizard(){
    try { sessionStorage.setItem('open_wizard', '1'); } catch(e){}
    window.location.href = base_url + '/Estudiantes';
}

function openModal()
{
    // FLUJO-ÓPTIMO (4): la creación va por el wizard único; este modal queda para editar/rematricular
    fntIrWizard();
}

// ITERACIÓN 2 (F-04): rematricular regular precargando la última matrícula.
function fntRematricular(ci){
    if(!ci){ swal("Atención", "CI inválido para rematricular.", "error"); return; }
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url+'/Matricula/ultimaMatricula?ci='+encodeURIComponent(ci), true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let o = JSON.parse(request.responseText);
            if(!o.status){
                // FLUJO: sin historial también se puede matricular (primera vez).
                // Se abre el modal con el CI precargado en gestión activa.
                if(o.msg && o.msg.indexOf('Sin matrículas previas') !== -1){
                    window._rematMode = true;
                    window._revConfirmed = false;
                    document.querySelector("#formNewMatricula").reset();
                    document.querySelector('#newG').value = "1";
                    document.querySelector('#idMatricula').value = "0";
                    document.querySelector('#txtCi').value = ci;
                    document.querySelector('#txtCi').disabled = true;
                    document.querySelector('#titleModal').innerHTML = "Matricular existente (" + ci + ")";
                    document.querySelector('#btnText').innerHTML = "Matricular";
                    let dest = new Date().getFullYear();
                    let bga = document.querySelector('#badgeGestionActiva');
                    if(bga && parseInt(bga.dataset.gestion)) dest = parseInt(bga.dataset.gestion);
                    document.querySelector('#intGestion').value = dest;
                    document.querySelector('#listStateInscripcion').value = 'Inscrito';
                    refreshPickers();
                    let boxM = document.querySelector('#boxMotivoEstado'); if(boxM) boxM.style.display = 'none';
                    let mr1 = document.querySelector('#motivoRectificacion'); if(mr1) mr1.value = "";
                    let bt0 = document.querySelector('#boxTipoMod'); if(bt0) bt0.style.display = 'none';
                    let be0 = document.querySelector('#boxEntregadoPor'); if(be0) be0.style.display = 'none';
                    setRematUI(true);
                    fntListParalelos(dest);
                    toggleRectBox();
                    $('#modalFormMatricula').modal('show');
                    // REV-MAT: mostrar a QUIÉN se registra (nombre desde el buscador)
                    let rqN = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                    rqN.open("GET", base_url + '/Estudiantes/buscar?q=' + encodeURIComponent(ci), true);
                    rqN.send();
                    rqN.onreadystatechange = function(){
                        if(rqN.readyState == 4 && rqN.status == 200){
                            try {
                                let on = JSON.parse(rqN.responseText);
                                if(on.status && on.data.length > 0){
                                    let s = on.data[0];
                                    document.querySelector('#titleModal').innerHTML = "Matricular existente: " + s.nombre + " " + s.apellido + " (" + s.ci + ")";
                                }
                            } catch(e){}
                        }
                    };
                    return;
                }
                swal("Error", o.msg, "error"); return;
            }
            let d = o.data;
            window._rematMode = true;
            window._revConfirmed = false;
            let rv = document.querySelector('#boxRevision'); if(rv) rv.style.display = 'none';
            let av = document.querySelector('#avisoGestionCerrada'); if(av) av.style.display = 'none';
            document.querySelector("#formNewMatricula").reset();
            document.querySelector('#newG').value = "1";
            document.querySelector('#idMatricula').value = "0";
            document.querySelector('#txtCi').value = d.ci_estudiante;
            document.querySelector('#txtCi').disabled = true;
            document.querySelector('#titleModal').innerHTML = "Rematricular: " + d.nombre_estudiante + " " + d.apellido_estudiante + " (" + d.ci_estudiante + ")";
            document.querySelector('#btnText').innerHTML = "Rematricular";
            let dest = o.gestion_activa || new Date().getFullYear();
            if(parseInt(d.gestion) >= dest){ dest = parseInt(d.gestion) + 1; }
            document.querySelector('#intGestion').value = dest;
            document.querySelector('#listTipoEstudiante').value = d.tipo_matricula || 'Regular';
            document.querySelector('#listStateInscripcion').value = 'Inscrito';
            refreshPickers();
            let boxM = document.querySelector('#boxMotivoEstado'); if(boxM) boxM.style.display = 'none';
            let mr1 = document.querySelector('#motivoRectificacion'); if(mr1) mr1.value = "";
            setRematUI(true);
            fntListParalelos(dest);
            toggleRectBox();
            swal("Rematriculación", "Última: gestión " + d.gestion + " · " + (d.curso || 'sin curso') + " · " + d.estado_inscripcion + ". Elija el nuevo paralelo.", "info");
            $('#modalFormMatricula').modal('show');
        }
    }
}

// REV-MAT2: los selects con widget selectpicker no muestran el valor
// programático hasta refrescarlos (era el dato "no reflejado" al editar).
function refreshPickers(){
    try {
        if(!window.jQuery) return;
        ['#listStateInscripcion','#listTipoEstudiante','#listParalelos'].forEach(function(sel){
            let $s = $(sel);
            if($s.length && $s.data('selectpicker')){ $s.selectpicker('refresh'); }
        });
    } catch(e){}
}
// REV-MAT: en modo rematricular/primera-vez el endpoint ignora estado y docs;
// se ocultan para no confundir (el estado será Inscrito).
function setRematUI(on){
    let st = document.querySelector('#listStateInscripcion');
    if(st){ st.disabled = !!on; }
    document.querySelectorAll('#modalFormMatricula .alert-warning').forEach(function(a){ a.style.display = on ? 'none' : ''; });
    // M06: etiqueta y entregado-por solo tienen sentido editando
    let bt = document.querySelector('#boxTipoMod'); if(bt) bt.style.display = on ? 'none' : '';
    let be = document.querySelector('#boxEntregadoPor'); if(be) be.style.display = on ? 'none' : '';
    if(!on){ toggleMotivoBox(); toggleRectBox(); }
}

// I3 (U-05): comprobante de matrícula imprimible
function fntComprobante(idMatricula){
    if(!idMatricula){ swal("Error", "Matrícula inválida.", "error"); return; }
    window.open(base_url + '/Matricula/comprobante/' + idMatricula, '_blank');
}

function fntViewMatricula(idMatricula){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Matricula/getMatricula/'+idMatricula;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                let d = objData.data;
                let extra = d.estado_inscripcion;
                if(d.plazo_documentos_hasta){ extra += " | Plazo docs: " + d.plazo_documentos_hasta; }
                if(d.compromiso_firmado == 1){ extra += " | Compromiso: sí"; }
                if(d.motivo_estado){ extra += " | Motivo: " + d.motivo_estado; }
                let base = "Estudiante: "+d.nombre_estudiante+" "+d.apellido_estudiante+" ("+d.ci_estudiante+") | Gestión: "+d.gestion+" | Tipo: "+d.tipo_matricula+" | "+extra;
                // M06: anexar último cambio registrado
                let rq2 = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                rq2.open("GET", base_url + '/Matricula/cambiosDe/' + idMatricula, true);
                rq2.send();
                rq2.onreadystatechange = function(){
                    let tail = "";
                    if(rq2.readyState == 4 && rq2.status == 200){
                        try {
                            let oc = JSON.parse(rq2.responseText);
                            if(oc.status && oc.data.length > 0){
                                let u = oc.data[0];
                                tail = " | Último cambio: " + u.tipo + (u.usuario ? " por " + u.usuario : "") + " (" + (u.fecha_reg || '').substring(0, 10) + ")";
                            }
                        } catch(e){}
                    }
                    if(rq2.readyState == 4){ swal("Matrícula "+d.id_matricula, base + tail, "info"); }
                };
            }else{
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntEditMatricula(element, idMatricula){
    window._rematMode = false;
    window._revConfirmed = false;
    let rv = document.querySelector('#boxRevision'); if(rv) rv.style.display = 'none';
    setRematUI(false);
    let bt = document.querySelector('#boxTipoMod'); if(bt) bt.style.display = '';
    let be = document.querySelector('#boxEntregadoPor'); if(be) be.style.display = '';
    let tm = document.querySelector('#tipoModificacion'); if(tm) tm.value = "";
    let ep = document.querySelector('#entregadoPor'); if(ep) ep.value = "";
    document.querySelector('#titleModal').innerHTML = "Actualizar Matrícula";
    document.querySelector('#btnText').innerHTML = "Actualizar";
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Matricula/getMatricula/'+idMatricula;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                let d = objData.data;
                document.querySelector('#newG').value = "0";
                document.querySelector('#idMatricula').value = d.id_matricula;
                document.querySelector('#txtCi').value = d.ci_estudiante;
                document.querySelector('#txtCi').disabled = true;
                document.querySelector('#intGestion').value = d.gestion;
                document.querySelector('#txtFolio').value = d.folio || "";
                document.querySelector('#listTipoEstudiante').value = d.tipo_matricula;
                document.querySelector('#listStateInscripcion').value = d.estado_inscripcion;
                refreshPickers();
                // ITERACIÓN 1: precarga documental
                let chk = document.querySelector('#chkDocPendiente');
                if(chk){ chk.checked = (d.estado_inscripcion === 'Pendiente_Documentos'); }
                let plz = document.querySelector('#plazoDocs');
                if(plz){ plz.value = d.plazo_documentos_hasta || ""; }
                try {
                    let docs = d.docs_checklist ? JSON.parse(d.docs_checklist) : null;
                    if(docs){
                        let c1 = document.querySelector('#modalFormMatricula [name="doc_cert_nac"]'); if(c1) c1.checked = !!docs.cert_nac;
                        let c2 = document.querySelector('#modalFormMatricula [name="doc_rude"]'); if(c2) c2.checked = !!docs.rude;
                        let c3 = document.querySelector('#modalFormMatricula [name="doc_solicitud"]'); if(c3) c3.checked = !!docs.solicitud;
                    }
                    let cc = document.querySelector('#chkCompromiso'); if(cc && d.compromiso_firmado != null) cc.checked = (d.compromiso_firmado == 1);
                    let ob = document.querySelector('#modalFormMatricula [name="docsObs"]'); if(ob) ob.value = d.docs_observacion || "";
                } catch(e){}
                // ITERACIÓN 2: motivo de estado terminal
                let mo = document.querySelector('#motivoEstado'); if(mo) mo.value = d.motivo_estado || "";
                toggleMotivoBox();
                toggleRectBox();
                // FLUJO-ÓPTIMO (6): aviso al tocar gestión cerrada
                let av2 = document.querySelector('#avisoGestionCerrada');
                let at2 = document.querySelector('#avisoGestionCerradaTxt');
                if(av2){
                    let ga = (typeof gestionActiva === 'function') ? gestionActiva() : 0;
                    if(ga > 0 && parseInt(d.gestion) !== ga){
                        if(at2) at2.textContent = 'Registro de gestión ' + d.gestion + ' (activa: ' + ga + ').';
                        av2.style.display = '';
                    }else{ av2.style.display = 'none'; }
                }
                fntListParalelos(d.gestion, d.id_paralelo);
                $('#modalFormMatricula').modal('show');
            }else{
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntDelMatricula(idMatricula){
    swal({
        title: "Dar de baja matrícula",
        text: "¿Realmente quiere dar de baja la matrícula?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, dar de baja",
        cancelButtonText: "No, cancelar",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm){
        if(isConfirm){
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Matricula/delMatricula';
            let formData = new FormData();
            formData.append('idMatricula', idMatricula);
            let tkd = document.querySelector('#formNewMatricula input[name="csrf_token"]');
            if(tkd){ formData.append('csrf_token', tkd.value); }
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        swal("Matrícula", objData.msg, "success");
                        tableMaterias.api().ajax.reload();
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                }
            }
        }
    });
}

    //FUNCIONES DEL SISTEMA -
    // ITERACIÓN 1: sincroniza check Pendiente <-> select estado
    // ITERACIÓN 2: muestra motivo en estados terminales
    document.addEventListener('change', function(e){
        if(e.target && e.target.id === 'chkDocPendiente'){
            let sel = document.querySelector('#listStateInscripcion');
            if(sel && e.target.checked){ sel.value = 'Pendiente_Documentos'; }
        }
        if(e.target && e.target.id === 'listStateInscripcion'){
            let chk = document.querySelector('#chkDocPendiente');
            if(chk){ chk.checked = (e.target.value === 'Pendiente_Documentos'); }
            toggleMotivoBox();
        }
        // I2-addenda: al cambiar el año, evaluar rectificación
        if(e.target && e.target.id === 'intGestion'){
            toggleRectBox();
        }
    });

function toggleMotivoBox(){
    let sel = document.querySelector('#listStateInscripcion');
    let box = document.querySelector('#boxMotivoEstado');
    if(!sel || !box) return;
    let v = sel.value;
    box.style.display = (v === 'Retirado' || v === 'Trasladado' || v === 'Egresado') ? '' : 'none';
    toggleRectBox();
}

// I2-addenda: motivo de rectificación si la gestión difiere de la activa
function gestionActiva(){
    let b = document.querySelector('#badgeGestionActiva');
    return b ? parseInt(b.dataset.gestion || '0') : 0;
}
function toggleRectBox(){
    let box = document.querySelector('#boxMotivoRect');
    let inp = document.querySelector('#intGestion');
    if(!box || !inp) return;
    let g = parseInt(inp.value || '0');
    let a = gestionActiva();
    box.style.display = (a > 0 && g > 0 && g !== a) ? '' : 'none';
}
window.addEventListener('load', function() {
        fntListParalelos();
        // ITERACIÓN 2: llegada desde ficha del estudiante → abre rematricular
        try {
            let ci = sessionStorage.getItem('remat_ci');
            if(ci){ sessionStorage.removeItem('remat_ci'); fntRematricular(ci); }
        } catch(e){}
}, false);

function fntListParalelos(gestion, selected){
    let year = gestion || new Date().getFullYear();
    if(document.querySelector('#listParalelos')){
        let ajaxUrl = base_url+'/Cursos/listParalelos/'+year;
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        request.open("GET",ajaxUrl,true);
        request.send();

        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                document.querySelector('#listParalelos').innerHTML = request.responseText;
                if(selected){
                    document.querySelector('#listParalelos').value = selected;
                }
                document.querySelector('#intGestion').value = year;
                // REV-MAT: si bootstrap-select ya vistió el combo, refrescarlo
                // ('render' no reconstruye la lista y lo dejaba vacío/inseleccionable).
                refreshPickers();
            }
        }
    }
}
