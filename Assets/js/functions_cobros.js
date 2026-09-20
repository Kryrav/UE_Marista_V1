let tableCobros;
document.addEventListener('DOMContentLoaded', function(){
    let divLoading = document.querySelector("#divLoading");
    if(document.querySelector("#tableCobros")){
        tableCobros = $('#tableCobros').dataTable({
            "aProcessing": true,
            "aServerSide": true,
            "language": { "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" },
            "ajax": { "url": " "+base_url+"/Cobros/getCobros", "dataSrc": "" },
            "columns": [
                {"data":"id_cobros"},
                {"data":"nombre"},
                {"data":"tipo"},
                {"data":"valor"},
                {"data":"status"},
                {"data":"options"}
            ],
            'dom': 'lBfrtip',
            'buttons': [
                {"extend":"copyHtml5","text":"<i class='far fa-copy'></i> Copiar","className":"btn btn-secondary"},
                {"extend":"excelHtml5","text":"<i class='fas fa-file-excel'></i> Excel","className":"btn btn-success"},
                {"extend":"pdfHtml5","text":"<i class='fas fa-file-pdf'></i> PDF","className":"btn btn-danger"},
                {"extend":"csvHtml5","text":"<i class='fas fa-file-csv'></i> CSV","className":"btn btn-info"}
            ],
            "bDestroy": true,
            "iDisplayLength": 10,
            "order": [[0,"desc"]]
        });
    }
    if(document.querySelector("#formCobro")){
        let formCobro = document.querySelector("#formCobro");
        formCobro.onsubmit = function(e){
            e.preventDefault();
            let nombre = document.querySelector('#txtTituloCobro').value.trim();
            let monto = parseFloat(document.querySelector('#txtMonto').value);
            if(nombre == '' || !(monto > 0)){
                swal("Atención", "Título y monto válido son obligatorios.", "error");
                return false;
            }
            if(divLoading) divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Cobros/saveCobro';
            let formData = new FormData(formCobro);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        if(tableCobros) tableCobros.api().ajax.reload();
                        $('#modalFormCobros').modal("hide");
                        formCobro.reset();
                        document.querySelector('#idCobro').value = "0";
                        swal("Cobros", objData.msg, "success");
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                    if(divLoading) divLoading.style.display = "none";
                }
            }
        }
    }
}, false);

function openModal()
{
    let f = document.querySelector("#formCobro");
    if(f) f.reset();
    document.querySelector('#idCobro').value = "0";
    document.querySelector('#titleModal').innerHTML = "Nuevo Cobro";
    $('#modalFormCobros').modal('show');
}

function openModalPago()
{
    $('#modalFormPagos').modal('show');
}

function fntViewCobro(id){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url+'/Cobros/getCobro/'+id, true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let o = JSON.parse(request.responseText);
            if(o.status){ swal("Cobro "+o.data.id_cobros, o.data.nombre+" | "+o.data.tipo+" | Bs. "+o.data.valor+" | "+(o.data.descripcion||""), "info"); }
            else{ swal("Error", o.msg, "error"); }
        }
    }
}

function fntEditCobro(id){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url+'/Cobros/getCobro/'+id, true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let o = JSON.parse(request.responseText);
            if(o.status){
                let d = o.data;
                document.querySelector('#idCobro').value = d.id_cobros;
                document.querySelector('#txtTituloCobro').value = d.nombre;
                document.querySelector('#txtMonto').value = d.valor;
                document.querySelector('#listTipoPago').value = d.tipo;
                document.querySelector('#txtDesPago').value = d.descripcion || "";
                document.querySelector('#intCuota').value = d.ncuota;
                document.querySelector('#statusCobro').value = d.status;
                document.querySelector('#titleModal').innerHTML = "Editar Cobro";
                $('#modalFormCobros').modal('show');
            }else{ swal("Error", o.msg, "error"); }
        }
    }
}

function fntDelCobro(id){
    swal({
        title: "Eliminar cobro",
        text: "¿Eliminar este cobro?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si",
        cancelButtonText: "No",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm){
        if(isConfirm){
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let formData = new FormData();
            formData.append('idCobro', id);
            request.open("POST", base_url+'/Cobros/delCobro', true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let o = JSON.parse(request.responseText);
                    if(o.status){ swal("OK", o.msg, "success"); if(tableCobros) tableCobros.api().ajax.reload(); }
                    else{ swal("Error", o.msg, "error"); }
                }
            }
        }
    });
}
