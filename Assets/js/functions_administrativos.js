let tableAdmins;
document.addEventListener('DOMContentLoaded', function(){
    fetch(base_url+'/Administrativos/roles').then(r=>r.text()).then(h=>{ if(document.querySelector("#listRol")) document.querySelector("#listRol").innerHTML=h; });
    if(document.querySelector("#tableAdmins")){
        tableAdmins = $('#tableAdmins').dataTable({"aProcessing":true,"aServerSide":true,"language":{"url":"https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"},"ajax":{"url":" "+base_url+"/Administrativos/getAdmins","dataSrc":""},"columns":[{"data":"ci"},{"data":"nombre"},{"data":"apellido"},{"data":"nombrerol"},{"data":"email"},{"data":"status"},{"data":"options"}],"bDestroy":true,"iDisplayLength":10});
    }
    if(document.querySelector("#formAdmin")){
        document.querySelector("#formAdmin").onsubmit = function(e){
            e.preventDefault();
            let fd = new FormData(this);
            let r = new XMLHttpRequest(); r.open("POST", base_url+'/Administrativos/saveAdmin', true); r.send(fd);
            r.onreadystatechange = function(){ if(r.readyState==4&&r.status==200){ let o=JSON.parse(r.responseText); if(o.status){ tableAdmins.api().ajax.reload(); $('#modalAdmin').modal('hide'); swal("OK",o.msg,"success"); } else swal("Error",o.msg,"error"); } }
        }
    }
}, false);
function openModalAdmin(){ document.querySelector("#formAdmin").reset(); document.querySelector("#idPersona").value="0"; $('#modalAdmin').modal('show'); }
function fntViewAdmin(id){ fetch(base_url+'/Administrativos/getAdmin/'+id).then(r=>r.json()).then(o=>{ if(o.status) swal("Admin", o.data.nombre+" "+o.data.apellido,"info"); }); }
function fntEditAdmin(id){ fetch(base_url+'/Administrativos/getAdmin/'+id).then(r=>r.json()).then(o=>{ if(o.status){ let d=o.data; document.querySelector("#idPersona").value=d.id_persona; document.querySelector("#txtCiA").value=d.ci; document.querySelector("#txtNombreA").value=d.nombre; document.querySelector("#txtApellidoA").value=d.apellido; document.querySelector("#txtCelA").value=d.cel; document.querySelector("#txtEmailA").value=d.email; document.querySelector("#txtDireccionA").value=d.direccion_dom||""; document.querySelector("#listRol").value=d.id_rol; $('#modalAdmin').modal('show'); } }); }
function fntDelAdmin(id){ swal({title:"Eliminar",text:"¿Dar de baja?",type:"warning",showCancelButton:true,confirmButtonText:"Si",closeOnConfirm:false},function(is){ if(is){ let fd=new FormData(); fd.append('idPersona',id); let r=new XMLHttpRequest(); r.open("POST",base_url+'/Administrativos/delAdmin',true); r.send(fd); r.onreadystatechange=function(){ if(r.readyState==4&&r.status==200){ let o=JSON.parse(r.responseText); swal(o.status?"OK":"Error",o.msg,o.status?"success":"error"); if(o.status) tableAdmins.api().ajax.reload(); } } } }); }
