let tableDocentes;
document.addEventListener('DOMContentLoaded', function(){
    if(document.querySelector("#tableDocentes")){
        tableDocentes = $('#tableDocentes').dataTable({"aProcessing":true,"aServerSide":true,"language":{"url":"https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"},"ajax":{"url":" "+base_url+"/Docentes/getDocentes","dataSrc":""},"columns":[{"data":"ci"},{"data":"nombre"},{"data":"apellido"},{"data":"cel"},{"data":"email"},{"data":"status"},{"data":"options"}],"bDestroy":true,"iDisplayLength":10});
    }
    if(document.querySelector("#formDocente")){
        document.querySelector("#formDocente").onsubmit = function(e){
            e.preventDefault();
            let fd = new FormData(this);
            let r = new XMLHttpRequest(); r.open("POST", base_url+'/Docentes/saveDocente', true); r.send(fd);
            r.onreadystatechange = function(){ if(r.readyState==4&&r.status==200){ let o=JSON.parse(r.responseText); if(o.status){ tableDocentes.api().ajax.reload(); $('#modalDocente').modal('hide'); swal("OK",o.msg,"success"); } else swal("Error",o.msg,"error"); } }
        }
    }
}, false);
function openModalDocente(){ document.querySelector("#formDocente").reset(); document.querySelector("#idPersona").value="0"; $('#modalDocente').modal('show'); }
function fntViewDocente(id){ fetch(base_url+'/Docentes/getDocente/'+id).then(r=>r.json()).then(o=>{ if(o.status) swal("Docente", o.data.nombre+" "+o.data.apellido+" ("+o.data.ci+")","info"); }); }
function fntEditDocente(id){ fetch(base_url+'/Docentes/getDocente/'+id).then(r=>r.json()).then(o=>{ if(o.status){ let d=o.data; document.querySelector("#idPersona").value=d.id_persona; document.querySelector("#txtCi").value=d.ci; document.querySelector("#txtNombre").value=d.nombre; document.querySelector("#txtApellido").value=d.apellido; document.querySelector("#txtCel").value=d.cel; document.querySelector("#txtEmail").value=d.email; document.querySelector("#txtDireccion").value=d.direccion_dom||""; $('#modalDocente').modal('show'); } }); }
function fntDelDocente(id){ swal({title:"Eliminar",text:"¿Dar de baja?",type:"warning",showCancelButton:true,confirmButtonText:"Si",closeOnConfirm:false},function(is){ if(is){ let fd=new FormData(); fd.append('idPersona',id); let r=new XMLHttpRequest(); r.open("POST",base_url+'/Docentes/delDocente',true); r.send(fd); r.onreadystatechange=function(){ if(r.readyState==4&&r.status==200){ let o=JSON.parse(r.responseText); swal(o.status?"OK":"Error",o.msg,o.status?"success":"error"); if(o.status) tableDocentes.api().ajax.reload(); } } } }); }
