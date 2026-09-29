<?php 
/**
 * Usuarios.php
 * Controlador para gestión de usuarios
 * Adaptado para usar Persona 
 */

class Usuarios extends Controllers {
    
    private $persona;
    
    public function __construct()
    {
        parent::__construct();
        session_start();
        
        if(empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            exit();
        }
        getPermisos(2);
    }

    /**
     * Vista principal de usuarios
     */
    public function index()
    {
        if(empty($_SESSION['permisosMod']['r'])) {
            header("Location: " . base_url() . '/dashboard');
            exit();
        }
        
        $data['page_tag'] = "Usuarios";
        $data['page_title'] = "USUARIOS <small>Gestión de usuarios del sistema</small>";
        $data['page_name'] = "usuarios";
        $data['page_functions_js'] = "functions_usuarios.js";
        
        $this->views->getView($this, "usuarios", $data);
    }

    /**
     * Guardar o actualizar usuario
     */
    public function setUsuario()
    {
        if($_POST) {
            // Validación básica de campos requeridos
            $camposRequeridos = [
                'txtCi', 'txtNombre', 'txtApellido', 'txtCel', 
                'txtEmail', 'listRolid', 'listStatus', 'txtDireccion', 'txtSexo','txtUserAcces'
            ];
            
            foreach ($camposRequeridos as $campo) {
                if (empty($_POST[$campo])) {
                    $this->jsonResponse(false, 'Datos incorrectos. Campo ' . $campo . ' requerido.');
                    return;
                }
            }
            
            // Recoger y limpiar datos
            $idUsuario = intval($_POST['idUsuario'] ?? 0);
            
            $data = [
                'ci' => strClean($_POST['txtCi']),
                'nombre' => ucwords(strClean($_POST['txtNombre'])),
                'apellido' => ucwords(strClean($_POST['txtApellido'])),
                'sexo' => ucwords(strClean($_POST['txtSexo'])),
                'direccion_dom' => strClean($_POST['txtDireccion']),
                'cel' => strClean($_POST['txtCel']),
                'email' => strtolower(strClean($_POST['txtEmail'])),
                // REV-SVC: usuario por defecto en política única
                'usuario' => \Services\UserPolicy::loginFor(
                    strClean($_POST['txtUserAcces'] ?? ''),
                    strtolower(strClean($_POST['txtEmail']))
                ),
                'id_rol' => intval(strClean($_POST['listRolid'])),
                'status' => intval(strClean($_POST['listStatus']))
            ];
            
            // Manejo de contraseña
            if (!empty($_POST['txtPassword'])) {
                $data['password'] = $_POST['txtPassword']; // Se encriptará en el modelo
            } elseif ($idUsuario == 0) {
                // Para nuevo usuario sin contraseña, usar CI como contraseña por defecto
                $data['password'] = $_POST['txtCi'];
            }
            
            try {
                // Verificar permisos
                if ($idUsuario == 0 && !$_SESSION['permisosMod']['w']) {
                    $this->jsonResponse(false, 'No tiene permisos para crear usuarios');
                    return;
                }
                
                if ($idUsuario > 0 && !$_SESSION['permisosMod']['u']) {
                    $this->jsonResponse(false, 'No tiene permisos para editar usuarios');
                    return;
                }
                
                // Ejecutar la operación correspondiente
                if ($idUsuario == 0) {
                    $result = $this->model->insertPersona($data);
                } else {
                    $result = $this->model->updatePersona($idUsuario, $data);
                }
                
                // Procesar respuesta
                if ($result['status'] === RESPONSE_SUCCESS) {
                    $mensaje = ($idUsuario == 0) 
                        ? 'Datos guardados correctamente.' 
                        : 'Datos actualizados correctamente.';
                    
                    $this->jsonResponse(true, $mensaje);
                } 
                // ✅ CORRECCIÓN 2: Usar elseif para los demás casos
                elseif ($result['status'] === RESPONSE_EXISTS) {
                    $campos = isset($result['duplicados']) 
                        ? implode(', ', $result['duplicados']) 
                        : 'Email, CI o Celular';
                    $this->jsonResponse(false, "¡Atención! $campos ya existe(n), ingrese otro.");
                } 
                elseif ($result['status'] === RESPONSE_ERROR) {
                    $mensaje = isset($result['errors']) 
                        ? 'Errores de validación: ' . json_encode($result['errors'])
                        : ($result['message'] ?? 'Error al procesar los datos.');
                    $this->jsonResponse(false, $mensaje);
                } 
                else {
                    $this->jsonResponse(false, $result['message'] ?? 'No es posible almacenar los datos.');
                }
             
            } catch (Exception $e) {
                error_log("Error en setUsuario: " . $e->getMessage());
                $this->jsonResponse(false, 'Error en el servidor: ' . $e->getMessage());
            }
        }
        die();
    }

    /**
     * Obtener listado de usuarios para DataTable
     */
    public function getUsuarios()
    {
        if($_SESSION['permisosMod']['r']) {
            try {
                $esAdmin = ($_SESSION['idUser'] == 1);
                $arrData = $this->model->selectPersonas($esAdmin);
                
                foreach ($arrData as $i => $usuario) {
                    // Formatear status
                    $arrData[$i]['status'] = $this->formatStatus($usuario['status']);

                    // Generar botones de acción
                    $arrData[$i]['options'] = $this->generateActionButtons($usuario);
                }
                
                echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
                
            } catch (Exception $e) {
                error_log("Error en getUsuarios: " . $e->getMessage());
                echo json_encode([], JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    /**
     * Obtener datos de un usuario específico
     */
    public function getUsuario($idpersona)
    {
        if($_SESSION['permisosMod']['r']) {
            $idusuario = intval($idpersona);
            
            if($idusuario > 0) {
                try {
                    $arrData = $this->model->selectPersona($idusuario);
                    
                    if(empty($arrData)) {
                        $this->jsonResponse(false, 'Datos no encontrados.');
                    } else {
                        // No enviar la contraseña por seguridad
                        unset($arrData['password']);
                        
                        echo json_encode([
                            'status' => true, 
                            'data' => $arrData
                        ], JSON_UNESCAPED_UNICODE);
                    }
                    
                } catch (Exception $e) {
                    error_log("Error en getUsuario: " . $e->getMessage());
                    $this->jsonResponse(false, 'Error al obtener los datos.');
                }
            }
        }
        die();
    }

    /**
     * Eliminar usuario (borrado lógico)
     */
public function delUsuario()
{
    if($_POST && $_SESSION['permisosMod']['d']) {
        $intIdpersona = intval($_POST['idUsuario'] ?? 0);
        
        if($intIdpersona > 0) {
            try {
                // Verificar que no se elimine a sí mismo
                if($intIdpersona == $_SESSION['idUser']) {
                    $this->jsonResponse(false, 'No puede eliminarse a sí mismo.');
                    return;
                }
                
                // Usar el método del modelo
                $result = $this->model->deleteUsuario($intIdpersona);
                
                // ✅ DEVOLVER RESPUESTA JSON SIEMPRE
                if($result['success']) {
                    $this->jsonResponse(true, 'Se ha eliminado el usuario');
                } else {
                    $this->jsonResponse(false, $result['message'] ?? 'Error al eliminar el usuario');
                }
                
            } catch (Exception $e) {
                error_log("Error en delUsuario: " . $e->getMessage());
                $this->jsonResponse(false, 'Error al eliminar el usuario.');
            }
        } else {
            // Si no hay ID válido
            $this->jsonResponse(false, 'ID de usuario no válido');
        }
    } else {
        // Si no hay POST o no tiene permisos
        $this->jsonResponse(false, 'No tiene permisos para esta acción');
    }
    die();
}

    /**
     * Vista de perfil de usuario
     */
    public function perfil()
    {
        $data['page_tag'] = "Perfil";
        $data['page_title'] = "Perfil de usuario";
        $data['page_name'] = "perfil";
        $data['page_functions_js'] = "functions_usuarios.js";
        
        $this->views->getView($this, "perfil", $data);
    }

    /**
     * Actualizar perfil del usuario logueado
     */
    public function putPerfil()
    {
        if($_POST) {
            // Validar campos requeridos
            $camposRequeridos = ['txtIdentificacion', 'txtNombre', 'txtApellido', 'txtTelefono'];
            
            foreach ($camposRequeridos as $campo) {
                if (empty($_POST[$campo])) {
                    $this->jsonResponse(false, 'Datos incorrectos.');
                    return;
                }
            }
            
            try {
                $idUsuario = $_SESSION['idUser'];

                // REV-SVC Fase 2: el merge + update + sesión viven en el modelo
                // (antes duplicado aquí y en updatePerfil con riesgo de divergir).
                $result = $this->model->updatePerfil(
                    $idUsuario,
                    strClean($_POST['txtIdentificacion']),
                    strClean($_POST['txtNombre']),
                    strClean($_POST['txtApellido']),
                    intval(strClean($_POST['txtTelefono'])),
                    strClean($_POST['txtPassword'] ?? '')
                );

                if(!empty($result['success'])) {
                    $this->jsonResponse(true, 'Datos actualizados correctamente.');
                } else {
                    $this->jsonResponse(false, $result['message'] ?? 'No es posible actualizar los datos.');
                }
                
            } catch (Exception $e) {
                error_log("Error en putPerfil: " . $e->getMessage());
                $this->jsonResponse(false, 'Error al actualizar el perfil.');
            }
        }
        die();
    }

    /**
     * Actualizar datos fiscales (si aplica)
     */
    public function putDFical()
    {
        if($_POST) {
            // Validar campos requeridos
            if(empty($_POST['txtNit']) || empty($_POST['txtNombreFiscal']) || empty($_POST['txtDirFiscal'])) {
                $this->jsonResponse(false, 'Datos incorrectos.');
                return;
            }
            
            try {
                $idUsuario = $_SESSION['idUser'];
                
                // Nota: Estos campos no están en tu tabla persona original
                // Deberías verificar si existen en tu BD o agregarlos
                $request_datafiscal = true; // Implementar según necesidad
                
                if($request_datafiscal) {
                    sessionUser($_SESSION['idUser']);
                    $this->jsonResponse(true, 'Datos actualizados correctamente.');
                } else {
                    $this->jsonResponse(false, 'No es posible actualizar los datos.');
                }
                
            } catch (Exception $e) {
                error_log("Error en putDFical: " . $e->getMessage());
                $this->jsonResponse(false, 'Error al actualizar datos fiscales.');
            }
        }
        die();
    }

    /**
     * Obtener pensiones de un estudiante (para el perfil)
     */
    public function getPensiones()
    {   
        $CiEstudiante = strClean($_POST['txtCiEstudiante'] ?? '');
        $html = '';
        
        if (empty($CiEstudiante)) {
            $this->jsonResponse(false, 'Datos no recibidos');
            return;
        }
        
        try {
            // Este método debería estar en un modelo de Pensiones
            // Por ahora asumimos que existe en el modelo actual
            $arrData = $this->model->selectPensiones($CiEstudiante);
            
            if ($arrData) {
                foreach ($arrData as $i => $pension) {
                    // Generar botones según estado y permisos
                    $btnImprimir = '';
                    $btnView = '';
                    $btnEdit = '';
                    $btnDelete = '';
                    
                    if($_SESSION['permisosMod']['r']) {
                        $btnView = '<button class="btn btn-info btn-sm btnViewMateria" onClick="fntViewPension(' . $pension['id_pensiones'] . ')" title="Ver Pago"><i class="far fa-eye"></i></button>';
                    }
                    
                    // REV-SVC: botones por regla única (antes: anular sin chequear estado)
                    if($_SESSION['permisosMod']['u'] && \Services\FinanzasService::puedePagar($pension)) {
                        $btnEdit = '<button class="btn btn-success btn-sm btnEditMateria" onClick="fntPagarPension(' . $pension['id_pensiones'] . ')" title="Pagar"><i class="fa fa-money"></i></button>';
                    }
                    
                    if($_SESSION['permisosMod']['d'] && \Services\FinanzasService::puedeAnular($pension)) {
                        $btnDelete = '<button class="btn btn-danger btn-sm btnDelMateria" onClick="fntAnularPension(' . $pension['id_pensiones'] . ')" title="Anular"><i class="far fa-trash-alt"></i></button>';
                    }
                    
                    // Formatear estado (REV-SVC: misma inferencia que Pensiones, incluye Vencido)
                    $estPen = \Services\FinanzasService::estadoPension($pension);
                    $pension['estado_pago'] = $estPen['badge'];
                    if($estPen['clave'] === 'pagado') {
                        $btnImprimir = '<button class="btn btn-secondary border-dark btn-sm" onClick="fntImprimirRecibo(' . $pension['id_pensiones'] . ')" title="Imprimir"><i class="fa fa-print"></i></button>';
                    }
                    
                    $pension['options'] = '<div class="text-center">' . $btnImprimir . ' ' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
                    
                    $html .= '<tr>
                        <td>' . $pension['matricula'] . '</td>
                        <td>' . $pension['gestion_academica'] . '</td>
                        <td>' . $pension['curso'] . '</td>
                        <td>' . $pension['mes_pago'] . '</td>
                        <td>' . $pension['monto_a_pagar'] . '</td>
                        <td>' . $pension['estado_pago'] . '</td>
                        <td>' . $pension['options'] . '</td>
                    </tr>';
                }
                
                echo json_encode([
                    'status' => true,
                    'msg' => 'Datos encontrados',
                    'ci' => $arrData[0]['ci_estudiante'],
                    'nombre' => $arrData[0]['nombre_completo'],
                    'tabla' => $html
                ], JSON_UNESCAPED_UNICODE);
                
            } else {
                $this->jsonResponse(false, 'No se encontraron datos del estudiante');
            }
            
        } catch (Exception $e) {
            error_log("Error en getPensiones: " . $e->getMessage());
            $this->jsonResponse(false, 'Error al consultar pensiones');
        }
        
        die();
    }

    /**
     * Método auxiliar para respuestas JSON
     */
    private function jsonResponse(bool $status, string $message, array $extra = [])
    {
        header('Content-Type: application/json');
        $response = array_merge([
            'status' => $status,
            'msg' => $message
        ], $extra);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Formatear status para mostrar en tabla
     * Convención: 1=Activo (verde) · 2=Inactivo (ámbar, visible sin acceso) · 0=Eliminado (oculto por el SP)
     */
    private function formatStatus($status): string
    {
        // REV-SVC: presentación única (misma tabla 1/2/0)
        return \Services\Presenter::estadoEstudiante(intval($status));
    }

    /**
     * Generar botones de acción según permisos
     */
    private function generateActionButtons(array $usuario): string
    {
        $btnView = '';
        $btnEdit = '';
        $btnDelete = '';
        
        // Botón Ver
        if($_SESSION['permisosMod']['r']) {
            $btnView = '<button class="btn btn-info btn-sm btnViewUsuario" onClick="fntViewUsuario(' . $usuario['id_persona'] . ')" title="Ver usuario"><i class="far fa-eye"></i></button>';
        }
        
        // Botón Editar - con lógica de permisos (REV-SVC: regla única en UserPolicy)
        if($_SESSION['permisosMod']['u']) {
            $puedeEditar = \Services\UserPolicy::puedeEditar($_SESSION, $usuario);
            
            if($puedeEditar) {
                $btnEdit = '<button class="btn btn-primary btn-sm btnEditUsuario" onClick="fntEditUsuario(this,' . $usuario['id_persona'] . ')" title="Editar usuario"><i class="fas fa-pencil-alt"></i></button>';
            } else {
                $btnEdit = '<button class="btn btn-secondary btn-sm" disabled><i class="fas fa-pencil-alt"></i></button>';
            }
        }
        
        // Botón Eliminar - con lógica de permisos (REV-SVC: regla única en UserPolicy)
        if($_SESSION['permisosMod']['d']) {
            $puedeEliminar = \Services\UserPolicy::puedeEliminar($_SESSION, $usuario);
            
            if($puedeEliminar) {
                $btnDelete = '<button class="btn btn-danger btn-sm btnDelUsuario" onClick="fntDelUsuario(' . $usuario['id_persona'] . ')" title="Eliminar usuario"><i class="far fa-trash-alt"></i></button>';
            } else {
                $btnDelete = '<button class="btn btn-secondary btn-sm" disabled><i class="far fa-trash-alt"></i></button>';
            }
        }
        
        return '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
    }

    /**
     * Método para debug (solo desarrollo)
     */
    public function variablesSession()
    {
        if(defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            dep($_SESSION);
        }
    }
}
?>