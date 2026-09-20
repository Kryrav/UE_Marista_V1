let tableTutores;
document.addEventListener('DOMContentLoaded', function(){
    let divLoading = document.querySelector("#divLoading");
    if(document.querySelector("#tableTutores")){
        tableTutores = $('#tableTutores').dataTable({
            // Cliente: el backend devuelve todo; búsqueda y orden locales.
            "processing": true, "serverSide": false,
            "language": { "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" },
            "ajax": { "url": " "+base_url+"/Tutores/getTutores", "dataSrc": "" },
            "columns": [
                {"data":"ci_tutor"},{"data":"nombre_tutor"},{"data":"apellido_tutor"},
                {"data":"tipo_parentesco"},{"data":"estudiante"},{"data":"cel_tutor"},
                {"data":"status_padre"},{"data":"options"}
            ],
            'dom': 'lBfrtip',
            'buttons': [
                {"extend":"copyHtml5","text":"<i class='far fa-copy'></i> Copiar","className":"btn btn-secondary"},
                {"extend":"excelHtml5","text":"<i class='fas fa-file-excel'></i> Excel","className":"btn btn-success"},
                {"extend":"pdfHtml5","text":"<i class='fas fa-file-pdf'></i> PDF","className":"btn btn-danger"},
                {"extend":"csvHtml5","text":"<i class='fas fa-file-csv'></i> CSV","className":"btn btn-info"}
            ],
            "bDestroy": true, "iDisplayLength": 10, "order": [[1,"asc"]],
            "initComplete": function(){ actualizarResumenTut(); }
        });
        tableTutores.on('draw.dt', function(){ actualizarResumenTut(); });
        let fPar = document.querySelector('#filtroParentesco');
        if(fPar){
            fPar.onchange = function(){
                // Parentesco es texto plano en la columna 3: match exacto
                let esc = this.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                tableTutores.api().column(3).search(this.value ? ('^' + esc + '$') : '', true, false).draw();
            };
        }
    }
    fntListEstudiantesTutor();
    // Autocomplete de estudiantes (CI/RUDE/nombre) para asignar el vínculo
    let busEst = document.querySelector('#buscarEstudiante');
    let timerBus = null;
    if(busEst){
        busEst.addEventListener('input', function(){
            clearTimeout(timerBus);
            let q = this.value.trim();
            let box = document.querySelector('#resultEstudiante');
            document.querySelector('#listEstudiante').value = "";
            document.querySelector('#chipEstudiante').innerHTML = "";
            document.querySelector('#tutoresActuales').innerHTML = "";
            if(q.length < 2){ box.style.display = 'none'; box.innerHTML = ""; return; }
            timerBus = setTimeout(function(){
                let r = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                r.open("GET", base_url + '/Tutores/buscarEstudiante?q=' + encodeURIComponent(q), true);
                r.send();
                r.onreadystatechange = function(){
                    if(r.readyState == 4 && r.status == 200){
                        let o = JSON.parse(r.responseText);
                        if(o.status && o.data.length > 0){
                            let h = '';
                            o.data.forEach(function(e){
                                h += '<div class="tut-opt" data-id="' + e.id_estudiante + '" data-label="' + escHtmlTut(e.ci + ' · ' + e.nombre + ' ' + e.apellido) + '">'
                                    + '<b>' + escHtmlTut(e.ci) + '</b> ' + escHtmlTut(e.nombre) + ' ' + escHtmlTut(e.apellido)
                                    + '<small class="text-muted"> · RUDE ' + escHtmlTut(e.rude) + (e.curso ? ' · ' + escHtmlTut(e.curso) : '')
                                    + ' · ' + e.tutores + ' tutor(es)</small></div>';
                            });
                            box.innerHTML = h;
                            box.style.display = '';
                            box.querySelectorAll('.tut-opt').forEach(function(el){
                                el.onclick = function(){ seleccionarEstudiante(el.dataset.id, el.dataset.label); };
                            });
                        }else{
                            box.innerHTML = '<div class="tut-opt text-muted">Sin coincidencias</div>';
                            box.style.display = '';
                        }
                    }
                };
            }, 300);
        });
        document.addEventListener('click', function(e){
            let box = document.querySelector('#resultEstudiante');
            if(box && !e.target.closest('#resultEstudiante') && e.target.id !== 'buscarEstudiante'){ box.style.display = 'none'; }
        });
    }
    // Lookup de CI: si el tutor ya existe, prellena (no se duplica persona)
    let ciTut = document.querySelector('#txtCiTutor');
    if(ciTut){
        ciTut.addEventListener('blur', function(){
            let ci = this.value.trim();
            let av = document.querySelector('#avisoTutorExiste');
            if(ci === '' || document.querySelector('#idPadre').value !== "0"){ if(av) av.style.display = 'none'; return; }
            let r = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            r.open("GET", base_url + '/Tutores/existeTutor/' + encodeURIComponent(ci), true);
            r.send();
            r.onreadystatechange = function(){
                if(r.readyState == 4 && r.status == 200){
                    let o = JSON.parse(r.responseText);
                    if(o.status && o.esTutor){
                        let d = o.data;
                        document.querySelector('#txtNombreTutor').value = d.nombre || "";
                        document.querySelector('#txtApellidoTutor').value = d.apellido || "";
                        document.querySelector('#listSexoTutor').value = d.sexo || "M";
                        document.querySelector('#txtCelTutor').value = d.cel || "";
                        document.querySelector('#txtEmailTutor').value = d.email || "";
                        if(av) av.style.display = '';
                    }else if(av){
                        av.style.display = 'none';
                    }
                }
            };
        });
    }
    let chkDom = document.querySelector('#chkMismoDom');
    let inpDom = document.querySelector('#txtDireccionTutor');
    let selEst = document.querySelector('#listEstudiante');
    window.refrescarDomicilioTutor = function(){
        if(chkDom && chkDom.checked && selEst && selEst.value){
            let r = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            r.open("GET", base_url + '/Estudiantes/getEstudiante/' + selEst.value, true);
            r.send();
            r.onreadystatechange = function(){
                if(r.readyState == 4 && r.status == 200){
                    let o = JSON.parse(r.responseText);
                    let dir = o.status && o.ficha ? (o.ficha.estudiante.direccion_dom || '') : '';
                    inpDom.value = dir;
                    inpDom.readOnly = true;
                }
            };
        }else if(inpDom){
            inpDom.readOnly = false;
        }
    };
    if(chkDom){ chkDom.onchange = window.refrescarDomicilioTutor; }
    if(selEst){ selEst.addEventListener('change', function(){ if(chkDom && chkDom.checked){ window.refrescarDomicilioTutor(); } }); }
    if(document.querySelector("#formTutor")){
        let f = document.querySelector("#formTutor");
        f.onsubmit = function(e){
            e.preventDefault();
            if(document.querySelector('#listEstudiante').value == ''){
                swal("Atención","Busque y seleccione el estudiante con el autocomplete.","error"); return false;
            }
            if(document.querySelector('#txtCiTutor').value.trim()==''||document.querySelector('#txtNombreTutor').value.trim()==''||document.querySelector('#txtApellidoTutor').value.trim()==''){
                swal("Atención","Complete los datos del tutor.","error"); return false;
            }
            if(divLoading) divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            request.open("POST", base_url+'/Tutores/saveTutor', true);
            request.send(new FormData(f));
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let o = JSON.parse(request.responseText);
                    if(o.status){
                        if(tableTutores) tableTutores.api().ajax.reload();
                        $('#modalFormTutores').modal("hide"); f.reset();
                        document.querySelector('#idPadre').value = "0";
                        swal("Tutores", o.msg, "success");
                    }else{ swal("Error", o.msg, "error"); }
                    if(divLoading) divLoading.style.display = "none";
                }
            }
        }
    }
}, false);

function fntListEstudiantesTutor(selected){
    // Obsoleto: el vínculo ahora se elige con el buscador autocomplete.
    // Se conserva por compatibilidad; al editar se usa seleccionarEstudiante().
    if(selected){ seleccionarEstudiante(selected, ''); }
}

// Fija el estudiante elegido: chip visible + tutores actuales del alumno
function seleccionarEstudiante(id, label){
    document.querySelector('#listEstudiante').value = id;
    document.querySelector('#resultEstudiante').style.display = 'none';
    document.querySelector('#buscarEstudiante').value = '';
    let chip = document.querySelector('#chipEstudiante');
    // Si no vino etiqueta (edición), la resolvemos por el endpoint de ficha
    if(!label){
        let r = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        r.open("GET", base_url + '/Estudiantes/getEstudiante/' + id, true);
        r.send();
        r.onreadystatechange = function(){
            if(r.readyState == 4 && r.status == 200){
                let o = JSON.parse(r.responseText);
                if(o.status){
                    let d = o.ficha ? o.ficha.estudiante : o.data;
                    pintarChipEstudiante(id, d.ci + ' · ' + d.nombre + ' ' + d.apellido);
                }
            }
        };
    }else{
        pintarChipEstudiante(id, label);
    }
    // Tutores actuales del alumno (evita duplicar el mismo parentesco sin ver)
    let t = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    t.open("GET", base_url + '/Tutores/getByEstudiante/' + id, true);
    t.send();
    t.onreadystatechange = function(){
        if(t.readyState == 4 && t.status == 200){
            let o = JSON.parse(t.responseText);
            let box = document.querySelector('#tutoresActuales');
            if(o.status && o.data.length > 0){
                let h = '<small class="text-muted">Tutores actuales:</small> ';
                o.data.forEach(function(x){
                    h += '<span class="badge badge-info mr-1">' + escHtmlTut(x.nombre + ' ' + x.apellido + ' (' + x.tipo_parentesco + ')') + '</span>';
                });
                box.innerHTML = h;
            }else{
                box.innerHTML = '<small class="text-muted">Sin tutores registrados.</small>';
            }
        }
    };
    // Si marcó mismo domicilio, refresca con la dirección de este alumno
    if(window.refrescarDomicilioTutor){ window.refrescarDomicilioTutor(); }
}

function pintarChipEstudiante(id, label){
    document.querySelector('#chipEstudiante').innerHTML =
        '<span class="badge badge-primary tut-chip"><i class="fa fa-graduation-cap"></i> ' + escHtmlTut(label || ('ID ' + id)) + '</span>';
}

function actualizarResumenTut(){
    try{
        let info = tableTutores.api().page.info();
        let el = document.querySelector('#resumenTut');
        if(el) el.innerHTML = 'Mostrando <b>' + info.recordsDisplay + '</b> de <b>' + info.recordsTotal + '</b> vínculos tutor-estudiante';
    }catch(e){}
}

function openModalTutor(){
    document.querySelector("#formTutor").reset();
    document.querySelector('#idPadre').value = "0";
    document.querySelector('#chkMismoDom').checked = false;
    document.querySelector('#txtDireccionTutor').readOnly = false;
    document.querySelector('#buscarEstudiante').value = "";
    document.querySelector('#listEstudiante').value = "";
    document.querySelector('#chipEstudiante').innerHTML = "";
    document.querySelector('#tutoresActuales').innerHTML = "";
    document.querySelector('#resultEstudiante').style.display = 'none';
    document.querySelector('#avisoTutorExiste').style.display = 'none';
    document.querySelector('#titleModalTutor').innerHTML = "Nuevo Tutor";
    $('#modalFormTutores').modal('show');
}

function escHtmlTut(s){
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function fntViewTutor(id){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url+'/Tutores/getTutor/'+id, true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let o = JSON.parse(request.responseText);
            if(o.status){
                let d = o.data;
                let ini = ((d.nombre || '').trim().charAt(0) + (d.apellido || '').trim().charAt(0)).toUpperCase() || '?';
                let extras = '';
                if(d.profesion) extras += '<div class="tut-row"><i class="fa fa-briefcase"></i><span>' + escHtmlTut(d.profesion) + (d.empresa_trabajo ? ' · ' + escHtmlTut(d.empresa_trabajo) : '') + '</span></div>';
                let meta = [d.nacionalidad, d.estado_civil].filter(Boolean).map(escHtmlTut).join(' · ');
                if(meta) extras += '<div class="tut-row"><i class="fa fa-id-card-o"></i><span>' + meta + '</span></div>';
                if(d.observaciones) extras += '<div class="tut-row"><i class="fa fa-sticky-note-o"></i><span>' + escHtmlTut(d.observaciones) + '</span></div>';
                document.querySelector('#listaTutoresEst').innerHTML =
                    '<div class="tut-card">'
                    + '<div class="tut-avatar">' + escHtmlTut(ini) + '</div>'
                    + '<div class="tut-body">'
                    + '<div class="tut-nombre">' + escHtmlTut(d.nombre) + ' ' + escHtmlTut(d.apellido) + ' <span class="badge badge-warning">' + escHtmlTut(d.tipo_parentesco) + '</span></div>'
                    + '<div class="tut-row"><i class="fa fa-id-card"></i><span>CI ' + escHtmlTut(d.ci) + '</span></div>'
                    + '<div class="tut-contacto">'
                    + '<a class="btn btn-success btn-sm" href="tel:' + escHtmlTut((d.cel || '').replace(/[^0-9+]/g, '')) + '"><i class="fa fa-phone"></i> ' + escHtmlTut(d.cel || '—') + '</a> '
                    + '<a class="btn btn-info btn-sm" href="mailto:' + escHtmlTut(d.email || '') + '"><i class="fa fa-envelope"></i> ' + escHtmlTut(d.email || '—') + '</a>'
                    + '</div>'
                    + '<div class="tut-row"><i class="fa fa-home"></i><span>' + escHtmlTut(d.direccion_dom || 'Sin dirección registrada') + '</span></div>'
                    + extras
                    + '<div class="tut-row"><i class="fa fa-graduation-cap"></i><span>Apoderado de <b>' + escHtmlTut(d.estudiante || '') + '</b></span></div>'
                    + '</div></div>';
                document.querySelector('#modalTutoresEstTitle').innerHTML = '<i class="fa fa-user"></i> ' + escHtmlTut(d.nombre) + ' ' + escHtmlTut(d.apellido);
                $('#modalTutoresEst').modal('show');
            }else{ swal("Error", o.msg, "error"); }
        }
    }
}

function fntEditTutor(id){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url+'/Tutores/getTutor/'+id, true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let o = JSON.parse(request.responseText);
            if(o.status){
                let d = o.data;
                document.querySelector('#idPadre').value = d.id_padre;
                document.querySelector('#txtCiTutor').value = d.ci;
                document.querySelector('#txtNombreTutor').value = d.nombre;
                document.querySelector('#txtApellidoTutor').value = d.apellido;
                document.querySelector('#listSexoTutor').value = d.sexo;
                document.querySelector('#txtCelTutor').value = d.cel;
                document.querySelector('#txtEmailTutor').value = d.email;
                document.querySelector('#txtDireccionTutor').value = d.direccion_dom || "";
                document.querySelector('#txtDireccionTutor').readOnly = false;
                document.querySelector('#chkMismoDom').checked = false;
                document.querySelector('#listStatusTutor').value = d.status_persona;
                document.querySelector('#listParentesco').value = d.tipo_parentesco;
                document.querySelector('#txtNacionalidad').value = d.nacionalidad || "";
                document.querySelector('#listEstadoCivil').value = d.estado_civil || "";
                document.querySelector('#txtProfesion').value = d.profesion || "";
                document.querySelector('#txtEmpresa').value = d.empresa_trabajo || "";
                document.querySelector('#txtObservaciones').value = d.observaciones || "";
                document.querySelector('#buscarEstudiante').value = "";
                document.querySelector('#avisoTutorExiste').style.display = 'none';
                fntListEstudiantesTutor(d.id_estudiante);
                document.querySelector('#titleModalTutor').innerHTML = "Editar Tutor";
                $('#modalFormTutores').modal('show');
            }else{ swal("Error", o.msg, "error"); }
        }
    }
}

function fntDelTutor(id){
    swal({title:"Dar de baja",text:"¿Dar de baja este tutor?",type:"warning",showCancelButton:true,confirmButtonText:"Si",cancelButtonText:"No",closeOnConfirm:false,closeOnCancel:true},
    function(isConfirm){
        if(isConfirm){
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let fd = new FormData(); fd.append('idPadre', id);
            request.open("POST", base_url+'/Tutores/delTutor', true);
            request.send(fd);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let o = JSON.parse(request.responseText);
                    if(o.status){ swal("OK", o.msg, "success"); if(tableTutores) tableTutores.api().ajax.reload(); }
                    else{ swal("Error", o.msg, "error"); }
                }
            }
        }
    });
}

// Ver tutores desde Estudiantes
function fntTutoresEstudiante(idEstudiante){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url+'/Tutores/getByEstudiante/'+idEstudiante, true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let o = JSON.parse(request.responseText);
            if(o.status && o.data.length > 0){
                let txt = o.data.map(t=>t.nombre+" "+t.apellido+" ("+t.tipo_parentesco+" - "+t.cel+")").join("\n");
                swal("Tutores", txt, "info");
            }else{ swal("Tutores","Sin tutores registrados. Asigne uno desde el módulo Tutores.","warning"); }
        }
    }
}
