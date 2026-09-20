let tableMaterias;
let rowTable = "";
let divLoading = document.querySelector("#divLoading");
document.addEventListener('DOMContentLoaded', function(){

    tableMaterias = $('#tableMaterias').dataTable( {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Materias/getMaterias",
            "dataSrc":""
        },
        "columns":[
            {"data":"nivel"},
            {"data":"grado"},
            {"data":"nombre_mat"},
            {"data":"area_mat"},
            {"data":"status"},
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

    if(document.querySelector("#formMateria")){
        let formMateria = document.querySelector("#formMateria");//Nombre del Formulario "formMateria"
        formMateria.onsubmit = function(e) {
            e.preventDefault();
            //Valores de los campos del formulario
            let intIdMateria = document.querySelector('#idMateria').value;
            let strArea = document.querySelector('#txtArea').value;
            let strNombre = document.querySelector('#txtCampo').value;
            let strDescripcion = document.querySelector('#txtDescripcion').value;
            let strNivel = document.querySelector('#listNivel').value;
            let intGrado = document.querySelector('#listGrado').value;
            let intStatus = document.querySelector('#Status').value;
            let intHoras = document.querySelector('#horas').value;
            
          
            //Validamos si ingresa los campos necesarios
            if(strArea == '' || strNombre == '' || strNivel == '' || intGrado == '' || intStatus == '')
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
            let ajaxUrl = base_url+'/Materias/insertNewMateria'; 
            let formData = new FormData(formMateria);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        if(rowTable == ""){
                            tableMaterias.api().ajax.reload(); //cuidar el table materias
                        }else{

                            // En resumen, este código permite que la tabla de usuarios se actualice automáticamente en la interfaz de usuario sin necesidad de recargar toda la página. PARA ACTUALIZAR                                
                            htmlStatus = intStatus == 1 ? 
                            '<span class="badge badge-success">Activo</span>' : 
                            '<span class="badge badge-danger">Inactivo</span>';
                            rowTable.cells[0].textContent = strNivel;
                            rowTable.cells[1].textContent = intGrado;
                            rowTable.cells[2].textContent = strNombre;
                            rowTable.cells[3].textContent = strArea;
                            rowTable.cells[4].innerHTML = htmlStatus;
                            rowTable="";
                        }
                        $('#modalFormMateria').modal("hide");
                        formMateria.reset();
                        swal("Materias", objData.msg ,"success");
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





function fntViewMateria(idMateria){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Materias/getMateria/'+idMateria;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);

            if(objData.status)
            {
               let estadoMateria = objData.data.status == 1 ? 
                '<span class="badge badge-success">Activo</span>' : 
                '<span class="badge badge-danger">Inactivo</span>';

                document.querySelector("#celidMateria").innerHTML = objData.data.id_materia;
                document.querySelector("#celtxtArea").innerHTML = objData.data.area_mat;
                document.querySelector("#celtxtCampo").innerHTML = objData.data.nombre_mat;
                document.querySelector("#celtxtDescripcion").innerHTML = objData.data.descripcion_mat;
                document.querySelector("#cellistNivel").innerHTML = objData.data.nivel;
                document.querySelector("#cellistGrado").innerHTML = objData.data.grado;
                document.querySelector("#celStatus").innerHTML = estadoMateria;
                document.querySelector("#celhoras").innerHTML = objData.data.horas_mat; 
                document.querySelector("#celfechaRegistro").innerHTML = objData.data.fechaRegistro; 

                $('#modalViewMateria').modal('show');
            }else{
                swal("Error", objData.msg , "error");
            }
        }
    }
}

function fntEditMateria(element,idMateria){
    rowTable = element.parentNode.parentNode.parentNode; 
    document.querySelector('#titleModal').innerHTML ="Actualizar Materia";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML ="Actualizar";

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Materias/getMateria/'+idMateria;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){

        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);

            if(objData.status)
            {
                
                //Preparamos los campos del mismo formulario
                document.querySelector("#idMateria").value = objData.data.id_materia;
                document.querySelector("#txtArea").value = objData.data.area_mat;
                document.querySelector("#txtCampo").value = objData.data.nombre_mat;
                document.querySelector("#txtDescripcion").value = objData.data.descripcion_mat;
                document.querySelector("#listNivel").value = objData.data.nivel;
                document.querySelector("#listGrado").value = objData.data.grado;
                document.querySelector("#horas").value =objData.data.horas_mat;

                $('#listNivel').selectpicker('render'); //Prepara la lista del tipo de usuario a registrar 
                $('#listGrado').selectpicker('render'); //Prepara la lista del tipo de usuario a registrar 

                if(objData.data.status == 1){
                    document.querySelector("#Status").value = 1;
                }else{
                    document.querySelector("#Status").value = 2;
                }
                $('#listStatus').selectpicker('render');//Prepara la lista del tipo de usuario a registrar 
            }
        }
        $('#modalFormMateria').modal('show'); //Todos los campor que llenamos estan en el FORMULARIO DE MATERIA
        
        
    }
}

function fntDelMateria(idMateria){
    swal({
        title: "Eliminar Materia",
        text: "¿Realmente quiere eliminar la materia?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        
        if (isConfirm) 
        {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Materias/delMateria';
            let strData = "id_Materia="+idMateria;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        swal("Eliminar!", objData.msg , "success");
                        tableMaterias.api().ajax.reload();
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


function openModal()
{
    document.querySelector('#idMateria').value ="0";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML ="Guardar";
    document.querySelector('#titleModal').innerHTML = "Nueva Materia";
    document.querySelector("#formMateria").reset();
    $('#modalFormMateria').modal('show');
}

