/**
 * functions_usuarios.js
 * Funciones para la gestión de usuarios
 */

let tableUsuarios;
let rowTable = "";
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable
    initDataTable();
    
    // Inicializar formularios
    initFormUsuario();
    initFormPerfil();
    initFormDataFiscal();
    
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

/**
 * Inicializar DataTable de usuarios
 */
/**
 * Inicializar DataTable de usuarios con botones de exportación
 */
function initDataTable() {
    if (!document.querySelector("#tableUsuarios")) {
        console.warn("No se encontró la tabla #tableUsuarios");
        return;
    }
    
    // Destruir instancia existente si la hay
    if ($.fn.DataTable.isDataTable('#tableUsuarios')) {
        $('#tableUsuarios').DataTable().destroy();
    }
    
    // Configuración de botones de exportación
    var buttons = [
        {
            text: '<i class="fas fa-sync-alt"></i> Actualizar',
            className: 'btn btn-outline-secondary btn-sm',
            action: function(e, dt, node, config) {
                dt.ajax.reload(null, false);
                swal("Actualizado", "Tabla actualizada correctamente", "success");
            }
        },
        {
            extend: 'copy',
            text: '<i class="far fa-copy"></i> Copiar',
            className: 'btn btn-outline-secondary btn-sm',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6]
            }
        },
        {
            extend: 'excel',
            text: '<i class="fas fa-file-excel"></i> Excel',
            className: 'btn btn-outline-success btn-sm',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6]
            }
        },
        {
            extend: 'pdf',
            text: '<i class="fas fa-file-pdf"></i> PDF',
            className: 'btn btn-outline-danger btn-sm',
            orientation: 'landscape',
            pageSize: 'A4',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6]
            }
        },
        {
            extend: 'csv',
            text: '<i class="fas fa-file-csv"></i> CSV',
            className: 'btn btn-outline-info btn-sm',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6]
            }
        },
        {
            extend: 'print',
            text: '<i class="fas fa-print"></i> Imprimir',
            className: 'btn btn-outline-primary btn-sm',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6]
            }
        }
    ];
    
    tableUsuarios = $('#tableUsuarios').DataTable({
        "processing": true,
        "serverSide": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
            "processing": "Procesando...",
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "zeroRecords": "No se encontraron registros",
            "paginate": {
                "first": "Primero",
                "previous": "Anterior",
                "next": "Siguiente",
                "last": "Último"
            }
        },
        "ajax": {
            "url": base_url + "/Usuarios/getUsuarios",
            "dataSrc": "",
            "error": function(xhr, error, code) {
                console.error("Error al cargar usuarios:", error);
                console.error("Respuesta:", xhr.responseText);
                swal("Error", "Error al cargar los datos", "error");
            }
        },
        "columns": [
            { 
                "data": "ci",
                "className": "text-center"
            },
            { 
                "data": "nombre",
                "className": "text-left"
            },
            { 
                "data": "apellido",
                "className": "text-left"
            },
            { 
                "data": "email",
                "className": "text-left"
            },
            { 
                "data": "cel",
                "className": "text-center"
            },
            { 
                "data": "nombrerol",
                "className": "text-center"
            },
            { 
                "data": "status",
                "className": "text-center"
            },
            { 
                "data": "options",
                "className": "text-center",
                "render": function(data) {
                    return data || '<span class="text-muted">-</span>';
                },
                "orderable": false
            }
        ],
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>" +
               "<'row'<'col-sm-12 mt-3'B>>", // Botones en nueva fila
        "buttons": buttons,
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]],
        "initComplete": function(settings, json) {
            console.log("DataTable inicializado correctamente");
            console.log("Registros cargados:", json ? json.length : 0);
        },
        "drawCallback": function(settings) {
            // Inicializar tooltips después de cada dibujado
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    
    // Asignar a window para acceso global (para los botones manuales)
    window.tableUsuarios = tableUsuarios;
}
/**
 * Inicializar formulario de usuario
 */
function initFormUsuario() {
    let formUsuario = document.querySelector("#formUsuario");
    if (!formUsuario) return;
    
    formUsuario.onsubmit = async function(e) {
        e.preventDefault();
        
        if (!validateUsuarioForm()) return;
        
        let idUsuario = document.querySelector('#idUsuario').value;
        let password = document.querySelector('#txtPassword').value;
        
        if (idUsuario === '' && password === '') {
            swal("Atención", "La contraseña es requerida para nuevos usuarios", "warning");
            return;
        }
        
        if (password !== '' && password.length < 6) {
            swal("Atención", "La contraseña debe tener al menos 6 caracteres", "warning");
            return;
        }
        
        divLoading.style.display = "flex";
        
        try {
            let formData = new FormData(formUsuario);
            
            let response = await fetch(base_url + '/Usuarios/setUsuario', {
                method: 'POST',
                body: formData
            });
            
            let data = await response.json();
            console.log("Formulario de usuario enviado:", formData);
            divLoading.style.display = "none";
            
            if (data.status) {
                if (tableUsuarios) {
                    tableUsuarios.ajax.reload(null, false);
                }
                
                $('#modalFormUsuario').modal('hide');
                formUsuario.reset();
                
                $('#listRolid').selectpicker('refresh');
                $('#listStatus').selectpicker('refresh');
                $('#txtSexo').selectpicker('refresh');
                
                swal("Éxito", data.msg, "success");
            } else {
                swal("Error", data.msg, "error");
            }
            
        } catch (error) {
            divLoading.style.display = "none";
            console.error("Error:", error);
            swal("Error", "Error en la comunicación con el servidor", "error");
        }
    };
}

/**
 * Validar formulario de usuario
 */
function validateUsuarioForm() {
    let campos = [
        { id: 'txtCi', nombre: 'CI' },
        { id: 'txtNombre', nombre: 'Nombre' },
        { id: 'txtApellido', nombre: 'Apellido' },
        { id: 'txtEmail', nombre: 'Email' },
        { id: 'txtCel', nombre: 'Celular' },
        { id: 'listRolid', nombre: 'Tipo de usuario' },
        { id: 'txtSexo', nombre: 'Sexo' },
        { id: 'txtDireccion', nombre: 'Dirección' }
    ];
    
    // Validar email
    let email = document.querySelector('#txtEmail').value;
    if (!isValidEmail(email)) {
        swal("Atención", "El correo electrónico no es válido", "warning");
        return false;
    }
    
    // Validar teléfono
    let cel = document.querySelector('#txtCel').value;
    if (!isValidPhone(cel)) {
        swal("Atención", "El número de teléfono debe tener entre 7 y 15 dígitos", "warning");
        return false;
    }
    
    // Validar campos requeridos
    for (let campo of campos) {
        let elemento = document.querySelector('#' + campo.id);
        if (!elemento || elemento.value.trim() === '') {
            swal("Atención", "El campo " + campo.nombre + " es obligatorio", "warning");
            return false;
        }
    }
    
    return true;
}

/**
 * Inicializar formulario de perfil
 */
function initFormPerfil() {
    let formPerfil = document.querySelector("#formPerfil");
    if (!formPerfil) return;
    
    formPerfil.onsubmit = async function(e) {
        e.preventDefault();
        
        // Validaciones...
        let campos = ['txtIdentificacion', 'txtNombre', 'txtApellido', 'txtTelefono'];
        for (let id of campos) {
            if (!document.querySelector('#' + id).value) {
                swal("Atención", "Todos los campos son obligatorios", "warning");
                return;
            }
        }
        
        let password = document.querySelector('#txtPassword').value;
        let passwordConfirm = document.querySelector('#txtPasswordConfirm').value;
        
        if (password !== '' || passwordConfirm !== '') {
            if (password !== passwordConfirm) {
                swal("Atención", "Las contraseñas no coinciden", "warning");
                return;
            }
            if (password.length < 6) {
                swal("Atención", "La contraseña debe tener al menos 6 caracteres", "warning");
                return;
            }
        }
        
        divLoading.style.display = "flex";
        
        try {
            let formData = new FormData(formPerfil);
            
            let response = await fetch(base_url + '/Usuarios/putPerfil', {
                method: 'POST',
                body: formData
            });
            
            let data = await response.json();
            
            divLoading.style.display = "none";
            
            if (data.status) {
                $('#modalFormPerfil').modal('hide');
                
                swal({
                    title: "Éxito",
                    text: data.msg,
                    type: "success",
                    confirmButtonText: "Aceptar"
                }, function() {
                    location.reload();
                });
            } else {
                swal("Error", data.msg, "error");
            }
            
        } catch (error) {
            divLoading.style.display = "none";
            console.error("Error:", error);
            swal("Error", "Error en la comunicación", "error");
        }
    };
}

/**
 * Inicializar formulario de datos fiscales
 */
function initFormDataFiscal() {
    let formDataFiscal = document.querySelector("#formDataFiscal");
    if (!formDataFiscal) return;
    
    formDataFiscal.onsubmit = function(e) {
        e.preventDefault();
        
        let campos = ['txtNit', 'txtNombreFiscal', 'txtDirFiscal'];
        for (let campo of campos) {
            let elemento = document.querySelector('#' + campo);
            if (!elemento || elemento.value.trim() === '') {
                showNotification("Todos los campos son obligatorios", "warning");
                return false;
            }
        }
        
        showLoading(true);
        
        let formData = new FormData(formDataFiscal);
        
        fetch(base_url + '/Usuarios/putDFical', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showLoading(false);
            
            if (data.status) {
                $('#modalFormPerfil').modal('hide');
                
                Swal.fire({
                    title: "¡Éxito!",
                    text: data.msg,
                    icon: "success",
                    confirmButtonText: "Aceptar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            } else {
                showNotification(data.msg, "error");
            }
        })
        .catch(error => {
            showLoading(false);
            console.error("Error:", error);
            showNotification("Error en la comunicación con el servidor", "error");
        });
    };
}

/**
 * Cargar roles en el select
 */
async function fntRolesUsuario() {
    if (!document.querySelector('#listRolid')) return;
    
    try {
        let response = await fetch(base_url + '/Roles/getSelectRoles');
        let html = await response.text();
        
        document.querySelector('#listRolid').innerHTML = html;
        
        if (typeof $ !== 'undefined' && $.fn.selectpicker) {
            $('#listRolid').selectpicker('render');
            $('#listRolid').selectpicker('refresh');
        }
    } catch (error) {
        console.error("Error al cargar roles:", error);
        swal("Error", "Error al cargar los roles", "error");
    }
}

/**
 * Ver usuario en modal
 */
async function fntViewUsuario(idpersona) {
    divLoading.style.display = "flex";
    
    try {
        let response = await fetch(base_url + '/Usuarios/getUsuario/' + idpersona);
        let data = await response.json();
        
        divLoading.style.display = "none";
        
        if (data.status) {
            let user = data.data;
            
            document.querySelector("#celCi").innerHTML = user.ci || '-';
            document.querySelector("#celNombre").innerHTML = user.nombre || '-';
            document.querySelector("#celApellido").innerHTML = user.apellido || '-';
            document.querySelector("#celCel").innerHTML = user.cel || '-';
            document.querySelector("#celEmail").innerHTML = user.email || '-';
            document.querySelector("#celTipoUsuario").innerHTML = user.nombrerol || '-';
            document.querySelector("#celEstado").innerHTML = formatStatus(user.status);
            document.querySelector("#celFechaRegistro").innerHTML = user.fecha_reg || '-';
            document.querySelector("#celDireccion").innerHTML = user.direccion_dom || '-';
            document.querySelector("#celSexo").innerHTML = user.sexo || '-';
            document.querySelector("#celUserAcces").innerHTML = user.usuario || '-';
            
            $('#modalViewUser').modal('show');
        } else {
            swal("Error", data.msg, "error");
        }
        
    } catch (error) {
        divLoading.style.display = "none";
        console.error("Error:", error);
        swal("Error", "Error al obtener los datos", "error");
    }
}

/**
 * Editar usuario
 */
async function fntEditUsuario(element, idpersona) {
    rowTable = element.closest('tr');
    
    document.querySelector('#titleModal').innerHTML = "Actualizar Usuario";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";
    
    divLoading.style.display = "flex";
    
    try {
        let response = await fetch(base_url + '/Usuarios/getUsuario/' + idpersona);
        let data = await response.json();
        
        divLoading.style.display = "none";
        
        if (data.status) {
            let user = data.data;
            
            document.querySelector("#idUsuario").value = user.id_persona || '';
            document.querySelector("#txtCi").value = user.ci || '';
            document.querySelector("#txtNombre").value = user.nombre || '';
            document.querySelector("#txtApellido").value = user.apellido || '';
            document.querySelector("#txtCel").value = user.cel || '';
            document.querySelector("#txtEmail").value = user.email || '';
            document.querySelector("#listRolid").value = user.idrol || '';
            document.querySelector("#listStatus").value = user.status || 1;
            document.querySelector("#txtSexo").value = user.sexo || '';
            document.querySelector("#txtDireccion").value = user.direccion_dom || '';
            document.querySelector("#txtUserAcces").value = user.usuario || '';
            document.querySelector("#txtPassword").value = '';
            
            if (typeof $ !== 'undefined' && $.fn.selectpicker) {
                $('#listRolid').selectpicker('render');
                $('#listRolid').selectpicker('refresh');
                $('#listStatus').selectpicker('render');
                $('#listStatus').selectpicker('refresh');
                $('#txtSexo').selectpicker('render');
                $('#txtSexo').selectpicker('refresh');
            }
            
            $('#modalFormUsuario').modal('show');
        } else {
            swal("Error", data.msg, "error");
        }
        
    } catch (error) {
        divLoading.style.display = "none";
        console.error("Error:", error);
        swal("Error", "Error al cargar los datos", "error");
    }
}

/**
 * Eliminar usuario
 */

function fntDelUsuario(idpersona) {
    swal({
        title: "Eliminar Usuario",
        text: "¿Realmente quiere eliminar este usuario?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false,
        closeOnCancel: true
    },
    async function(isConfirm) {
        if (isConfirm) {
            divLoading.style.display = "flex";
            
            try {
                let formData = new FormData();
                formData.append("idUsuario", idpersona);
                
                let response = await fetch(base_url + '/Usuarios/delUsuario', {
                    method: 'POST',
                    body: formData
                });
                
                let data = await response.json();
                console.log("Respuesta del servidor:", data);
                
                divLoading.style.display = "none";
                
                if (data.status) {
                    // Cerrar el loading de SweetAlert y mostrar éxito
                    swal({
                        title: "Eliminado",
                        text: data.msg,
                        type: "success",
                        confirmButtonText: "Aceptar"
                    }, function() {
                        if (tableUsuarios) {
                            tableUsuarios.ajax.reload();
                        }
                    });
                } else {
                    swal("Atención", data.msg, "error");
                }
                
            } catch (error) {
                divLoading.style.display = "none";
                console.error("Error en eliminación:", error);
                swal("Error", "Error en la comunicación", "error");
            }
        }
    });
}
/**
 * Abrir modal para nuevo usuario
 */
function openModal() {
    // Resetear formulario
    document.querySelector('#idUsuario').value = "";
    document.querySelector('#formUsuario').reset();
    document.querySelector('#txtPassword').value = "";
    
    // Configurar modal para creación
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Usuario";
    
    // Actualizar selects
    $('#listRolid').selectpicker('render');
    $('#listRolid').selectpicker('refresh');
    $('#listStatus').selectpicker('render');
    $('#listStatus').selectpicker('refresh');
    
    $('#modalFormUsuario').modal('show');
}

/**
 * Abrir modal de perfil
 */
function openModalPerfil() {
    $('#modalFormPerfil').modal('show');
}

/**
 * Buscar pensiones de estudiante
 */
function buscarPensionesCi(ci) {
    if (!ci) {
        showNotification("CI no válido", "error");
        return false;
    }
    
    showLoading(true);
    
    let formData = new FormData();
    formData.append("txtCiEstudiante", ci);
    
    fetch(base_url + '/Usuarios/getPensiones', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        showLoading(false);
        
        if (data.status) {
            document.querySelector('#celCiEstudiante').innerHTML = data.ci || '-';
            document.querySelector('#celNombreEstudiante').innerHTML = data.nombre || '-';
            document.querySelector('#listaPensionesEstudiante').innerHTML = data.tabla || '<tr><td colspan="7" class="text-center">No hay pensiones registradas</td></tr>';
            
            showNotification(data.msg, "success");
        } else {
            showNotification(data.msg, "error");
        }
    })
    .catch(error => {
        showLoading(false);
        console.error("Error:", error);
        showNotification("Error al consultar pensiones", "error");
    });
}

/**
 * Formatear status para mostrar
 */
function formatStatus(status) {
    status = parseInt(status);
    switch(status) {
        case 1:
            return '<span class="badge badge-success">Activo</span>';
        case 2:
            return '<span class="badge badge-warning">Inactivo</span>';
        case 0:
            return '<span class="badge badge-danger">Eliminado</span>';
        default:
            return '<span class="badge badge-secondary">Desconocido</span>';
    }
}

/**
 * Validar email
 */
function isValidEmail(email) {
    let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

/**
 * Validar teléfono
 */
function isValidPhone(phone) {
    let re = /^[0-9]{7,15}$/;
    return re.test(String(phone));
}

/**
 * Mostrar/ocultar loading
 */
function showLoading(show) {
    if (!divLoading) return;
    divLoading.style.display = show ? "flex" : "none";
}

/**
 * Mostrar notificación
 */
function showNotification(message, type = "info") {
    const types = {
        success: "success",
        error: "error",
        warning: "warning",
        info: "info"
    };
    
    Swal.fire({
        icon: types[type] || "info",
        title: message,
        showConfirmButton: true,
        timer: type === "success" ? 2000 : null
    });
}

// Cargar roles al iniciar
window.addEventListener('load', function() {
    fntRolesUsuario();
    
    // Validación en tiempo real para campos numéricos
    document.querySelectorAll('.validNumber').forEach(input => {
        input.addEventListener('keypress', function(e) {
            return controlTag(e);
        });
    });
});

/**
 * Control de teclas para números (función existente)
 */
function controlTag(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla == 8 || tecla == 32 || tecla == 0 || tecla == 13) {
        return true;
    }
    patron = /[0-9]/;
    te = String.fromCharCode(tecla);
    return patron.test(te);
}

/**
 * Exportar tabla a Excel
 */
function exportToExcel() {
    if (!tableUsuarios) {
        swal("Error", "La tabla no está inicializada", "error");
        return;
    }
    
    // Usar el botón de DataTable
    $('.buttons-excel').click();
}

/**
 * Exportar tabla a PDF
 */
function exportToPDF() {
    if (!tableUsuarios) {
        swal("Error", "La tabla no está inicializada", "error");
        return;
    }
    
    // Usar el botón de DataTable
    $('.buttons-pdf').click();
}

/**
 * Actualizar tabla
 */
function refreshTable() {
    if (tableUsuarios) {
        tableUsuarios.ajax.reload(null, false);
        swal("Actualizado", "Tabla actualizada correctamente", "success");
    }
}

// En functions_usuarios.js agregar:

// Mostrar/ocultar contraseña
function togglePassword(inputId, btn) {
    let input = document.getElementById(inputId);
    let icon = btn.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Validar fortaleza de contraseña
document.getElementById('txtPassword')?.addEventListener('input', function() {
    let password = this.value;
    let strengthBar = document.querySelector('#passwordStrengthBar .progress-bar');
    
    if (password.length === 0) {
        strengthBar.style.width = '0%';
        strengthBar.className = 'progress-bar';
        return;
    }
    
    let strength = 0;
    
    // Longitud mínima
    if (password.length >= 6) strength += 25;
    if (password.length >= 8) strength += 15;
    
    // Contiene números
    if (/\d/.test(password)) strength += 20;
    
    // Contiene minúsculas y mayúsculas
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 20;
    
    // Contiene caracteres especiales
    if (/[^a-zA-Z0-9]/.test(password)) strength += 20;
    
    strengthBar.style.width = strength + '%';
    
    if (strength < 40) {
        strengthBar.className = 'progress-bar bg-danger';
    } else if (strength < 70) {
        strengthBar.className = 'progress-bar bg-warning';
    } else {
        strengthBar.className = 'progress-bar bg-success';
    }
});

// Editar datos personales (pendiente de implementar)
function editarDatosPersonales() {
    // Implementar modal de edición rápida
    swal("Info", "Funcionalidad en desarrollo", "info");
}