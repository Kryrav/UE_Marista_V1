let currentGestionCurso = new Date().getFullYear();

document.addEventListener('DOMContentLoaded', function(){
    let divLoading = document.querySelector("#divLoading");
    let form = document.querySelector("#formCurso") || document.querySelector("#modalFormCurso form");
    if(form){
        form.onsubmit = function(e) {
            e.preventDefault();
            let formData = new FormData(form);
            let hid = document.querySelector('#modalFormCurso #idParalelo');
            if(hid && !formData.get('idParalelo')){ formData.append('idParalelo', hid.value); }
            if(divLoading) divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            request.open("POST", base_url+'/Cursos/saveParalelo', true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        swal("Curso", objData.msg, "success");
                        $('#modalFormCurso').modal("hide");
                        form.reset();
                        fntListarCursosCards(currentGestionCurso);
                    }else{
                        swal("Error", objData.msg, "error");
                    }
                    if(divLoading) divLoading.style.display = "none";
                }
            }
        }
    }
    let sel = document.querySelector('#selGestionCurso');
    if(sel){
        sel.value = String(currentGestionCurso);
        sel.onchange = function(){ fntListarCursosCards(parseInt(this.value)); };
    }
    let buscador = document.querySelector('#buscadorCurso');
    if(buscador){ buscador.addEventListener('input', aplicarFiltrosCursos); }
    let fNiv = document.querySelector('#filtroNivel');
    if(fNiv){ fNiv.onchange = aplicarFiltrosCursos; }
    let fEst = document.querySelector('#filtroEstado');
    if(fEst){ fEst.onchange = aplicarFiltrosCursos; }
}, false);

window.addEventListener('load', function() {
    fntListarCursosCards(currentGestionCurso);
}, false);

function fntListarCursosCards(gestion){
    let loading = document.querySelector("#divLoading");
    if(gestion){ currentGestionCurso = parseInt(gestion); }
    if(loading) loading.style.display = "flex";
    let box = document.querySelector('#CursosCard');
    if(!box){ if(loading) loading.style.display = "none"; return; }
    box.innerHTML = skeletonCursos();
    ocultarSinResultados();
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url+'/Cursos/listarcursos/'+currentGestionCurso, true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            box.innerHTML = request.responseText;
            if(loading) loading.style.display = "none";
            aplicarFiltrosCursos();
        }
    }
}

function skeletonCursos(){
    let html = '';
    for(let i = 0; i < 4; i++){
        html += '<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4 curso-skeleton">'
            + '<div class="card curso-card h-100"><div class="curso-banner">'
            + '<div class="sk" style="width:52px;height:52px;border-radius:50%;"></div>'
            + '<div style="flex:1;"><div class="sk" style="height:16px;width:70%;margin-bottom:8px;"></div>'
            + '<div class="sk" style="height:12px;width:45%;"></div></div></div>'
            + '<div class="card-body"><div class="sk" style="height:12px;margin-bottom:8px;"></div>'
            + '<div class="sk" style="height:12px;width:80%;margin-bottom:8px;"></div>'
            + '<div class="sk" style="height:8px;"></div></div>'
            + '<div class="card-footer"><div class="sk" style="height:32px;"></div></div>'
            + '</div></div>';
    }
    return html;
}

function aplicarFiltrosCursos(){
    let box = document.querySelector('#CursosCard');
    let resumen = document.querySelector('#resumenCursos');
    if(!box) return;
    let items = box.querySelectorAll('.curso-item');
    let q = ((document.querySelector('#buscadorCurso') || {}).value || '').toLowerCase().trim();
    let niv = (document.querySelector('#filtroNivel') || {value:'todos'}).value;
    let est = (document.querySelector('#filtroEstado') || {value:'todos'}).value;
    let vis = 0, insc = 0, cap = 0;
    items.forEach(function(el){
        let okQ = !q || (el.dataset.search || '').indexOf(q) !== -1;
        let okN = niv === 'todos' || el.dataset.nivel === niv;
        let okE = est === 'todos' || el.dataset.estado === est;
        let show = okQ && okN && okE;
        el.style.display = show ? '' : 'none';
        if(show){ vis++; insc += parseInt(el.dataset.inscritos || 0); cap += parseInt(el.dataset.cupo || 0); }
    });
    if(items.length === 0){
        ocultarSinResultados();
        if(resumen) resumen.innerHTML = 'Sin paralelos en la gestión <b>' + currentGestionCurso + '</b>.';
        return;
    }
    let pct = cap > 0 ? Math.round(insc / cap * 100) : 0;
    if(resumen) resumen.innerHTML = 'Mostrando <b>' + vis + '</b> de <b>' + items.length + '</b> paralelos &nbsp;·&nbsp; <b>' + insc + '</b> inscritos &nbsp;·&nbsp; Ocupación <b>' + pct + '%</b>';
    let sin = document.querySelector('#sinResultados');
    if(sin) sin.style.display = (vis === 0) ? '' : 'none';
}

function ocultarSinResultados(){
    let sin = document.querySelector('#sinResultados');
    if(sin) sin.style.display = 'none';
}

function fntViewCurso(idParalelo, gestion)
{
    let loading = document.querySelector("#divLoading");
    if(loading) loading.style.display = "flex";
    let year = gestion || currentGestionCurso;
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url+'/Cursos/viewCurso/'+idParalelo+'/'+year, true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            if(loading) loading.style.display = "none";
            let objData = JSON.parse(request.responseText);
            if(objData.statusLista)
            {
                let c = objData.dataCurso[0];
                document.querySelector("#datoCurso").value = c.nivel + ' ' + c.grado + ' "' + c.sigla + '"';
                document.querySelector("#datoParalelo").value = c.cupo;
                document.querySelector("#datoTurno").value = c.turno;
                document.querySelector("#datoTutor").value = c.tutor;
                document.querySelector("#datoInscritos").value = c.total_inscritos;
                document.querySelector("#datoEstado").value = c.status_paralelo;
                document.querySelector("#listarEstudiantes").value = c.id_paralelo;
                document.querySelector("#listCursoEstudiantes").innerHTML = objData.listaCurso || '<tr><td colspan="8" class="text-center text-muted">Sin estudiantes inscritos.</td></tr>';
                let btnPrint = document.querySelector("#btnImprimirLista");
                if(btnPrint){ btnPrint.setAttribute("onclick", "fntImprimirListaCurso("+year+")"); }
                $('#modalViewCurso').modal('show');
            }else{
                swal("Curso", objData.msg || "Sin datos del curso.", "warning");
            }
        }
    }
}

function cargarTutoresDocentes(selected){
    let sel = document.querySelector('#modalFormCurso #listTutorDocente');
    if(!sel) return;
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url + '/Cursos/docentesTutores', true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            sel.innerHTML = request.responseText;
            if(selected){ sel.value = selected; }
        }
    }
}

function openModalCurso(){
    let f = document.querySelector("#modalFormCurso form");
    if(f) f.reset();
    let hid = document.querySelector('#idParalelo');
    if(hid) hid.value = "0";
    let t = document.querySelector('#titleModalCurso');
    if(t) t.innerHTML = "Nuevo Paralelo";
    cargarTutoresDocentes(0);
    $('#modalFormCurso').modal('show');
}

function fntEditCurso(element, idParalelo){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url+'/Cursos/getParalelo/'+idParalelo, true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                let d = objData.data;
                let modal = document.querySelector('#modalFormCurso');
                modal.querySelector('#idParalelo').value = d.id_paralelo;
                cargarTutoresDocentes(d.id_persona_tutor || 0);
                if(d.id_persona_tutor === null && d.tutor && d.tutor !== 'Sin asignación'){
                    setTimeout(function(){ swal("Aviso", "El tutor actual no es docente registrado: seleccione uno de la lista.", "warning"); }, 400);
                }
                if(modal.querySelector('#listSigla')) modal.querySelector('#listSigla').value = d.sigla;
                if(modal.querySelector('#listTurno')) modal.querySelector('#listTurno').value = (d.turno == 'Tarde' ? 'T' : 'M');
                if(modal.querySelector('#listGrado')){
                    modal.querySelector('#listGrado').value = (d.nivel == 'Inicial') ? 'Inicial' : String(d.grado);
                }
                if(modal.querySelector('#titleModalCurso')) modal.querySelector('#titleModalCurso').innerHTML = "Editar Paralelo";
                $('#modalFormCurso').modal('show');
            }else{
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntDelCurso(idParalelo){
    swal({
        title: "Dar de baja",
        text: "¿Dar de baja este paralelo?",
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
            formData.append('idParalelo', idParalelo);
            request.open("POST", base_url+'/Cursos/delParalelo', true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){ swal("OK", objData.msg, "success"); fntListarCursosCards(currentGestionCurso); }
                    else{ swal("Error", objData.msg, "error"); }
                }
            }
        }
    });
}

function fntViewEstudiante(idEstudiante)
{
    let loading = document.querySelector("#divLoading");
    if(loading) loading.style.display = "flex";
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url+'/Estudiantes/getEstudiante/'+idEstudiante, true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            if(loading) loading.style.display = "none";
            let objData = JSON.parse(request.responseText);
            if(objData.status)
            {
                let estadoEstudiante = objData.data.estudiante_status == 1 ?
                '<span class="badge badge-success">Activo</span>' :
                '<span class="badge badge-danger">Inactivo</span>';
                document.querySelector("#celStudentCi").innerHTML = objData.data.ci;
                document.querySelector("#celStudentNombre").innerHTML = objData.data.nombre;
                document.querySelector("#celStudentApellido").innerHTML = objData.data.apellido;
                document.querySelector("#celStudentSexo").innerHTML = objData.data.sexo;
                document.querySelector("#celStudentFechaNacimiento").innerHTML = objData.data.fnacimiento;
                document.querySelector("#celStudentCiudad").innerHTML = objData.data.ciudad;
                document.querySelector("#celStudentPais").innerHTML = objData.data.pais;
                document.querySelector("#celStudentProvincia").innerHTML = objData.data.provincia;
                document.querySelector("#celStudentCelular").innerHTML = objData.data.cel;
                document.querySelector("#celStudentDomicilio").innerHTML = objData.data.direccion_dom;
                document.querySelector("#celStudentEmail").innerHTML = objData.data.email;
                document.querySelector("#celStudentUsuario").innerHTML = objData.data.usuario;
                document.querySelector("#celStudentEmergencia").innerHTML = objData.data.emergencia;
                document.querySelector("#celStudentRude").innerHTML = objData.data.rude;
                document.querySelector("#celStudentEstadoRegEst").innerHTML = objData.data.estado_reg;
                document.querySelector("#celStudentFechaRegEst").innerHTML = objData.data.estudiante_fecha_reg;
                document.querySelector("#celStudentColPRocedencia").innerHTML = objData.data.colegio_proc;
                document.querySelector("#celStudentStatus").innerHTML = estadoEstudiante;
                $('#modalViewEstudianteOther').modal('show');
            }else{
                swal("Error", objData.msg , "error");
            }
        }
    }
}

function fntImprimirListaCurso(gestion)
{
    let idCurso = document.querySelector('#listarEstudiantes').value;
    let year = gestion || currentGestionCurso;
    const width = 900;
    const height = 700;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;
    // Hoja limpia que se auto-imprime al abrir
    window.open(
      base_url+'/Cursos/lista/'+idCurso+'/'+year,
      "_blank",
      "width="+width+",height="+height+",top="+top+",left="+left+",resizable=yes,scrollbars=yes"
    );
}
