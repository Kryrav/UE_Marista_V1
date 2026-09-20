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
            let ajaxUrl = base_url+'/Matricula/insertNewMatricula/'; 
            let formData = new FormData(formMatricula);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
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
    document.querySelector("#formNewMatricula").reset();
    document.querySelector('#newG').value = "1";
    document.querySelector('#idMatricula').value = "0";
    document.querySelector('#txtCi').disabled = false;
    document.querySelector('#titleModal').innerHTML = "Nueva Matrícula";
    document.querySelector('#btnText').innerHTML = "Matricular Estudiante";
    let year = new Date().getFullYear();
    document.querySelector('#intGestion').value = year;
    fntListParalelos(year);
    $('#modalFormMatricula').modal('show');
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
                swal("Matrícula "+d.id_matricula, "Estudiante: "+d.nombre_estudiante+" "+d.apellido_estudiante+" ("+d.ci_estudiante+") | Gestión: "+d.gestion+" | Tipo: "+d.tipo_matricula+" | "+d.estado_inscripcion, "info");
            }else{
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntEditMatricula(element, idMatricula){
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
window.addEventListener('load', function() {
        fntListParalelos();
        
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
