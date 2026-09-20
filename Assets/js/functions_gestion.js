let tableGestiones;
let rowTable = "";
let divLoading = document.querySelector("#divLoading");
document.addEventListener('DOMContentLoaded', function(){

    tableGestiones = $('#tableGestiones').dataTable( {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Gestion/getGestionesPas",
            "dataSrc":""
        },
        "columns":[
            {"data":"gestion"},
            {"data":"inicio"},
            {"data":"fin"},
            {"data":"monto_pension"},
            {"data":"status"}
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

    if(document.querySelector("#formGestion")){
        let formGestion = document.querySelector("#formGestion");//Nombre del Formulario "formGestion"
        formGestion.onsubmit = function(e) {
            e.preventDefault();
            //Valores de los campos del formulario
            let intIdGestion = document.querySelector('#anio').value;
            let dateInicio = new Date (document.querySelector('#fechaInicio').value);
            let dateFin = new Date (document.querySelector('#fechaFin').value);
            let strGestion_L = document.querySelector('#anio').value;
            let intPension = document.querySelector('#intPension').value;
            let strDescripcion = document.querySelector('#txtDescripcion').value;
            
            let year = new Date().getFullYear();
            // No se puede abrir gestión mientras no Finalize el año
            if (intIdGestion > year) {
                swal("Atención", "No es posible aperturar la gestión: "+ intIdGestion,"error");
                document.querySelector("#formGestion").reset();
                $('#modalAbrirGestion').modal('hide');
                return false;
            }
            if (dateInicio >= dateFin) {
                swal("Atención", "Error en fechas asignadas","error");
                return false;
            }
          
            //Validamos si ingresa los campos necesarios
            if(intIdGestion == '' || dateInicio == '' || dateFin == '' || intPension == '' || strDescripcion == '')
            {
                swal("Atención", "Todos los campos son obligatorios." , "error");
                return false;
            }

            //Validamos los campos necesarios
            let elementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < elementsValid.length; i++) { 
                if(elementsValid[i].classList.contains('is-invalid')) { 
                    swal("Atención", "Por favor verifique los campos en rojo." , "error");
                    return false;
                } 
            } 
            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Gestion/insertGestion'; 
            let formData = new FormData(formGestion);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        fntCargarGestion();
                        tableGestiones.api().ajax.reload();
                        $('#modalAbrirGestion').modal("hide");
                        formGestion.reset();

                        //MSG 
                        swal("Gestión", objData.msg ,"success");
                    }else{
                        swal("Error", objData.msg , "error");
                    }
                }
                divLoading.style.display = "none";
                return false;
            }
        }
    }
  
 
}, false);


window.addEventListener('load', function() {
        fntCargarGestion();
}, false);


// CARGA LA GESTIÓN EN EL CUADRO ACTIVO 
function fntCargarGestion(){
    
    let year = new Date().getFullYear();
    if(document.querySelector('#gestion_actual')){
        // Controlamos lo botones de formulario de actualización y cerrar gestión.
        let botonUpdate = document.querySelector('.btn-updateGestion');
        let botonClose = document.querySelector('.btn-closeGestion');
        let botonOpen = document.querySelector('.btn-OpenGestion');

        
        let ajaxUrl = base_url+'/Gestion/getGestionAct/';
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        request.open("GET",ajaxUrl,true);
        request.send();
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                let objData = JSON.parse(request.responseText);
                if (objData.status) {
                    htmlStatus = objData.data.status == 1 ? 
                            '<span class="badge badge-success">Abierto</span>' : 
                            '<span class="badge badge-danger">Cerrado</span>';
                    document.querySelector('#cellGestion').innerHTML =  objData.data.gestion;
                    document.querySelector('#cellInicio').innerHTML =  objData.data.inicio;
                    document.querySelector('#cellFin').innerHTML =  objData.data.fin;
                    document.querySelector('#cellPension').innerHTML =  objData.data.monto_pension;
                    document.querySelector('#cellDescripcion').innerHTML =  objData.data.descripcion;
                    document.querySelector('#cellStatus').innerHTML =  htmlStatus;

                      // Cambiar el estilo del botón (opcional)
                      botonClose.disabled = false;
                      botonUpdate.disabled = false;
                      botonOpen.disabled    =   true;

                      botonUpdate.style.backgroundColor = ''; // Cambia el color de fondo si deseas darle un aspecto de deshabilitado
                      botonUpdate.style.cursor = '';   // Cambia el cursor para indicar que está desactivado
  
                      botonClose.style.backgroundColor = ''; // Cambia el color de fondo si deseas darle un aspecto de deshabilitado
                      botonClose.style.cursor = '';   // Cambia el cursor para indicar que está desactivado
                      
                }else{
                    swal("Atención!", objData.msg , "warning");

                    document.querySelector('#cellGestion').innerHTML =  "Cerrado";
                    document.querySelector('#cellInicio').innerHTML =  "Cerrado";
                    document.querySelector('#cellFin').innerHTML = "Cerrado";
                    document.querySelector('#cellPension').innerHTML =  "Cerrado";
                    document.querySelector('#cellDescripcion').innerHTML =  "Cerrado";
                    document.querySelector('#cellStatus').innerHTML =  "Cerrado";
                    
                    botonUpdate.disabled=true;
                    botonClose.disabled=true;
                    botonOpen.disabled    =   false;

                    // Cambiar el estilo del botón (opcional)
                    botonUpdate.style.backgroundColor = 'gray'; // Cambia el color de fondo si deseas darle un aspecto de deshabilitado
                    botonUpdate.style.cursor = 'not-allowed';   // Cambia el cursor para indicar que está desactivado

                    botonClose.style.backgroundColor = 'gray'; // Cambia el color de fondo si deseas darle un aspecto de deshabilitado
                    botonClose.style.cursor = 'not-allowed';   // Cambia el cursor para indicar que está desactivado
                }
                
            }
        }
    }
}


function updateGestion(){
    let year = new Date().getFullYear();
    let ajaxUrl = base_url+'/Gestion/getGestionAct';
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if (objData.status) {
                // htmlStatus = objData.data.status == 1 ? 
                //         '<span class="badge badge-success">Abierto</span>' : 
                //         '<span class="badge badge-danger">Cerrado</span>';
                document.querySelector('#anio').value =  objData.data.gestion;
                document.querySelector('#fechaInicio').value =  objData.data.inicio;
                document.querySelector('#fechaFin').value =  objData.data.fin;
                document.querySelector('#intPension').value =  objData.data.monto_pension;
                document.querySelector('#txtDescripcion').value =  objData.data.descripcion;
                document.querySelector('#newG').value =0; // Valor para determinar si es actualización o apertura de nueca gestion 
                $('#Status').selectpicker('render');//Prepara la lista del tipo de usuario a registrar 

                document.querySelector('#titleModal').innerHTML ="Actualizar Gestión";
                document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
                document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
                document.querySelector('#btnText').innerHTML ="Actualizar";

                $('#modalAbrirGestion').modal('show'); //Todos los campor que llenamos estan en el FORMULARIO DE GESTION
            }else{
                swal("Error", objData.msg , "error");
            }
            
        }
    }
       
}

function closeGestion(gestion){
    swal({
        title: "Cerrar Gestión",
        text: "¿Realmente quiere cerrar la gestión?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, cerrar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        
        if (isConfirm) 
        {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/gestion/closeGestion';
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send();
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        swal("Eliminar!", objData.msg , "success");
                        tableGestiones.api().ajax.reload();
                        fntCargarGestion ();
                        alert ("llego hasta aqui ");
                    }else{
                        swal("Atención!", objData.msg , "error");
                    }
                }else if (request.readyState == 4) {
                    swal("Data Error!", "Hubo un problema con la solicitud.", "error");
                }
            }
        }

    });

}



// function fntDelMateria(idMateria){
//     swal({
//         title: "Eliminar Materia",
//         text: "¿Realmente quiere eliminar la materia?",
//         type: "warning",
//         showCancelButton: true,
//         confirmButtonText: "Si, eliminar!",
//         cancelButtonText: "No, cancelar!",
//         closeOnConfirm: false,
//         closeOnCancel: true
//     }, function(isConfirm) {
        
//         if (isConfirm) 
//         {
//             let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
//             let ajaxUrl = base_url+'/Materias/delMateria';
//             let strData = "id_Materia="+idMateria;
//             request.open("POST",ajaxUrl,true);
//             request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//             request.send(strData);
//             request.onreadystatechange = function(){
//                 if(request.readyState == 4 && request.status == 200){
//                     let objData = JSON.parse(request.responseText);
//                     if(objData.status)
//                     {
//                         swal("Eliminar!", objData.msg , "success");
//                         tableMaterias.api().ajax.reload();
//                     }else{
//                         swal("Atención!", objData.msg , "error");
//                     }
//                 }else if (request.readyState == 4) {
//                     swal("Data Error!", "Hubo un problema con la solicitud.", "error");
//                 }
//             }
//         }

//     });

// }


function openNewGestion()
{
    document.querySelector('#newG').value =1;
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML ="Abrir Gestión";
    document.querySelector('#titleModal').innerHTML = "Nueva Gestión";
    document.querySelector("#formGestion").reset();
    $('#modalAbrirGestion').modal('show');
}

$('#tableGestiones').DataTable({
    autoWidth: true,
    responsive: true,  // Asegura que la tabla se ajuste a pantallas más pequeñas
});
