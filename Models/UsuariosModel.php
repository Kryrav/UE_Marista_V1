<?php
/**
 * UsuariosModel.php
 * Modelo para operaciones específicas de usuarios
 */

class UsuariosModel extends Persona {
    
    private $db;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Actualizar perfil del usuario
     */
    public function updatePerfil(
        int $idUsuario, 
        string $identificacion, 
        string $nombre, 
        string $apellido, 
        int $telefono, 
        string $password = ""
    ): array {
        
        try {
            $usuarioActual = $this->selectPersona($idUsuario);
            
            if (!$usuarioActual) {
                return $this->formatResponse(false, 'Usuario no encontrado');
            }
            
            $data = [
                'ci' => $identificacion,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'cel' => $telefono,
                'sexo' => $usuarioActual['sexo'],
                'direccion_dom' => $usuarioActual['direccion_dom'],
                'email' => $usuarioActual['email'],
                'usuario' => $usuarioActual['usuario'],
                'id_rol' => $usuarioActual['idrol'],
                'status' => $usuarioActual['status']
            ];
            
            if (!empty($password)) {
                $data['password'] = $password;
            }
            
            $result = $this->updatePersona($idUsuario, $data);
            
            // Si la actualización fue exitosa, actualizar sesión
            if ($result['success']) {
                sessionUser($idUsuario);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error en updatePerfil: " . $e->getMessage());
            return $this->formatResponse(false, 'Error al actualizar perfil');
        }
    }
    
    /**
     * Guardar datos fiscales
     */
    public function saveDataFiscal(
        int $idUsuario, 
        string $nit, 
        string $nombreFiscal, 
        string $direccionFiscal
    ): array {
        
        try {
            // Verificar si ya existe registro fiscal
            $existe = $this->select(
                "SELECT id_fiscal FROM datos_fiscales WHERE id_persona = ?",
                [$idUsuario]
            );
            
            if ($existe) {
                // Actualizar
                $sql = "UPDATE datos_fiscales SET 
                        nit = ?, 
                        nombre_fiscal = ?, 
                        direccion_fiscal = ? 
                        WHERE id_persona = ?";
                
                $result = $this->update($sql, [
                    $nit, 
                    $nombreFiscal, 
                    $direccionFiscal, 
                    $idUsuario
                ]);
            } else {
                // Insertar
                $sql = "INSERT INTO datos_fiscales 
                        (id_persona, nit, nombre_fiscal, direccion_fiscal) 
                        VALUES (?, ?, ?, ?)";
                
                $result = $this->insert($sql, [
                    $idUsuario, 
                    $nit, 
                    $nombreFiscal, 
                    $direccionFiscal
                ]);
            }
            
            return $this->formatResponse(
                (bool)$result, 
                $result ? 'Datos fiscales guardados' : 'Error al guardar datos fiscales'
            );
            
        } catch (Exception $e) {
            error_log("Error en saveDataFiscal: " . $e->getMessage());
            return $this->formatResponse(false, 'Error al guardar datos fiscales');
        }
    }
    
    /**
     * Obtener datos fiscales
     */
    public function getDataFiscal(int $idUsuario): ?array {
        try {
            return $this->select(
                "SELECT * FROM datos_fiscales WHERE id_persona = ? AND status = 1",
                [$idUsuario]
            );
        } catch (Exception $e) {
            error_log("Error en getDataFiscal: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Método para compatibilidad con el controlador actual
     * (El método selectPensiones debería estar en un modelo específico)
     */
    public function selectPensiones(int $ciEstudiante) {
        try {
            // Crear instancia del modelo de pensiones
            $pensionModel = new PensionModel();
            return $pensionModel->getPensionesByCI($ciEstudiante);
            
        } catch (Exception $e) {
            error_log("Error en selectPensiones: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Formatear respuesta
     */
    private function formatResponse(bool $success, string $message, array $data = []): array {
        return array_merge([
            'success' => $success,
            'status' => $success ? self::MSG_GUARDADO : self::MSG_ERROR,
            'message' => $message
        ], $data);
    }

    public function deleteUsuario(int $idUsuario): array
    {
        try {
            // Proteger usuario admin
            if ($idUsuario == 1) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar el usuario administrador'
                ];
            }
            
            // Usar el método heredado de BaseModel
            $result = $this->deleteById($idUsuario);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Usuario eliminado correctamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se pudo eliminar el usuario'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error en deleteUsuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos'
            ];
        }
    }
}