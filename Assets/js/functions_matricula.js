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
                        swal("Matrícula", objData.msg ,"success");
                    }else{
                        swal("Error", objData.msg , "error");
                    }                  
                }
                divLoading.style.display = "none";
            }
            
        }
    }
}, false);

function openModal()
{
    window._rematMode = false;
    document.querySelector("#formNewMatricula").reset();
    document.querySelector('#newG').value = "1";
    document.querySelector('#idMatricula').value = "0";
    document.querySelector('#txtCi').disabled = false;
    document.querySelector('#titleModal').innerHTML = "Nueva Matrícula";
    document.querySelector('#btnText').innerHTML = "Matricular Estudiante";
    let boxM = document.querySelector('#boxMotivoEstado'); if(boxM) boxM.style.display = 'none';
    let mr0 = document.querySelector('#motivoRectificacion'); if(mr0) mr0.value = "";
    let year = new Date().getFullYear();
    document.querySelector('#intGestion').value = year;
    fntListParalelos(year);
    toggleRectBox();
    $('#modalFormMatricula').modal('show');
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
            if(!o.status){ swal("Error", o.msg, "error"); return; }
            let d = o.data;
            window._rematMode = true;
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
            let boxM = document.querySelector('#boxMotivoEstado'); if(boxM) boxM.style.display = 'none';
            let mr1 = document.querySelector('#motivoRectificacion'); if(mr1) mr1.value = "";
            fntListParalelos(dest);
            toggleRectBox();
            swal("Rematriculación", "Última: gestión " + d.gestion + " · " + (d.curso || 'sin curso') + " · " + d.estado_inscripcion + ". Elija el nuevo paralelo.", "info");
            $('#modalFormMatricula').modal('show');
        }
    }
}

// I3 (U-05): comprobante de matrícula imprimible
function fntComprobante(idMatricula){
    if(!idMatricula){ swal("Error", "Matrícula inválida.", "error"); return; }
    window.open(base_url + '/Matricula/comprobante/' + idMatricula, '_blank');
}

function fntViewMatricula(idMatricula){    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
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
                swal("Matrícula "+d.id_matricula, "Estudiante: "+d.nombre_estudiante+" "+d.apellido_estudiante+" ("+d.ci_estudiante+") | Gestión: "+d.gestion+" | Tipo: "+d.tipo_matricula+" | "+extra, "info");
            }else{
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntEditMatricula(element, idMatricula){
    window._rematMode = false;
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
                if(window.jQuery && $('#listParalelos').selectpicker){
                    $('#listParalelos').selectpicker('render');
                }
            }
        }
    }
}
