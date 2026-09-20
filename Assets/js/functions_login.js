// functions_login.js - VERSIÓN MEJORADA

$('.login-content [data-toggle="flip"]').click(function() {
    $('.login-box').toggleClass('flipped');
    return false;
});

const divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function() {

    // Helper para peticiones fetch
    async function sendFormData(url, formData) {
        try {
            divLoading.style.display = "flex";
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error en la petición:', error);
            swal("Error", "Error en el proceso. Intente nuevamente.", "error");
            return null;
        } finally {
            divLoading.style.display = "none";
        }
    }

    // --- Formulario de Login ---
    const formLogin = document.querySelector("#formLogin");
    if (formLogin) {
            formLogin.onsubmit = async function(e) {
            e.preventDefault();

            let strEmail = document.querySelector('#txtEmail').value;
            let strPassword = document.querySelector('#txtPassword').value;

            console.log("Email enviado:", strEmail);
            console.log("Password enviado (texto plano):", strPassword);
            console.log("Longitud password:", strPassword.length);

            if(strEmail == "" || strPassword == "") {
                swal("Por favor", "Escribe usuario y contraseña.", "error");
                return false;
            }

            const formData = new FormData(formLogin);
            // Verificar que FormData contiene la contraseña en texto plano
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            const result = await sendFormData(base_url + '/Login/loginUser', formData);

            if (result && result.status) {
                window.location = base_url + '/dashboard';
            } else if (result) {
                swal("Atención", result.msg, "error");
                document.querySelector('#txtPassword').value = "";
            }
        };
    }

    // --- Formulario de Reset de Contraseña ---
    const formResetPass = document.querySelector("#formRecetPass");
    if (formResetPass) {
        formResetPass.onsubmit = async function(e) {
            e.preventDefault();

            const txtEmailReset = document.querySelector('#txtEmailReset').value.trim();
            if (!txtEmailReset) {
                swal("Por favor", "Escribe tu correo electrónico.", "error");
                return;
            }

            const formData = new FormData(formResetPass);
            const result = await sendFormData(base_url + '/Login/resetPass', formData);

            if (result && result.status) {
                swal({
                    title: "",
                    text: result.msg,
                    type: "success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false,
                }, function() {
                    window.location = base_url;
                });
            } else if (result) {
                swal("Atención", result.msg, "error");
            }
        };
    }

    // --- Formulario de Cambiar Contraseña ---
    const formCambiarPass = document.querySelector("#formCambiarPass");
    if (formCambiarPass) {
        formCambiarPass.onsubmit = async function(e) {
            e.preventDefault();

            const txtPassword = document.querySelector('#txtPassword').value;
            const txtPasswordConfirm = document.querySelector('#txtPasswordConfirm').value;

            if (!txtPassword || !txtPasswordConfirm) {
                swal("Por favor", "Escribe la nueva contraseña.", "error");
                return;
            }

            if (txtPassword.length < 5) {
                swal("Atención", "La contraseña debe tener un mínimo de 5 caracteres.", "info");
                return;
            }

            if (txtPassword !== txtPasswordConfirm) {
                swal("Atención", "Las contraseñas no son iguales.", "error");
                return;
            }

            const formData = new FormData(formCambiarPass);
            const result = await sendFormData(base_url + '/Login/setPassword', formData);

            if (result && result.status) {
                swal({
                    title: "",
                    text: result.msg,
                    type: "success",
                    confirmButtonText: "Iniciar sesión",
                    closeOnConfirm: false,
                }, function() {
                    window.location = base_url + '/login';
                });
            } else if (result) {
                swal("Atención", result.msg, "error");
            }
        };
    }

}, false);