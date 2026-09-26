<?php
    class MatriculaModel extends Mysql
    {
        private $id_matricula;
        private $id_estudiante;
        private $id_paralelo;
        private $id_user;
        private $gestion;
        private $tipo;
        private $folio;
        private $estado_inscripcion;
        private $status;
        private $fecha_reg;

        private $strCi_estudiante;

        public function __construct()
		{
			parent::__construct();
		}
        //LISTAR TODAS LAS MATRICULAS 
        public function selectMatriculasAll()
        {
            $sql = "CALL listar_matriculas();";
            $request = $this->select_all($sql);
            return $request;
        }

        // Insertar matricula nueva (genera 10 pensiones vía SP)
        public function insertMatricula(string $strCi, int $intGestion, int $intIdParalelo, string $strTipoMatricula, string $strFolio, string $strStatus)
        {
            try {
                $sql = "CALL matricular_estudiante_y_generar_pensiones(?,?,?,?,?,?)";
                $arrData = array($strCi, $intGestion, $intIdParalelo, $strTipoMatricula, $strFolio, $strStatus);
                $request_insert = $this->insert($sql, $arrData);
                if ($request_insert) {
                    return "matricula_guardada";
                }
                return "Error al guardar";
            } catch (Exception $e) {
                $msg = $e->getMessage();
                if (strpos($msg, 'ya est') !== false) {
                    return "matricula_existente";
                }
                return $msg;
            }
        }

        // Obtener una matrícula por ID (con datos de estudiante y paralelo)
        // ITERACIÓN 1: incluye columnas documentales si existen (compat hacia atrás)
        // ITERACIÓN 2: + motivo_estado (M02).
        public function selectMatricula(int $idMatricula)
        {
            try {
                $sql = "SELECT m.id_matricula, m.gestion, m.id_paralelo, m.tipo AS tipo_matricula,
                               m.folio, m.estado_inscripcion, m.status AS estado_matricula,
                               m.plazo_documentos_hasta, m.docs_checklist, m.compromiso_firmado, m.docs_observacion,
                               m.motivo_estado,
                               e.id_estudiante, p.ci AS ci_estudiante,
                               p.nombre AS nombre_estudiante, p.apellido AS apellido_estudiante
                        FROM matricula m
                        INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
                        INNER JOIN persona p ON e.id_persona = p.id_persona
                        WHERE m.id_matricula = ?";
                return $this->select($sql, [$idMatricula]);
            } catch (Exception $e) {
                $sql = "SELECT m.id_matricula, m.gestion, m.id_paralelo, m.tipo AS tipo_matricula,
                               m.folio, m.estado_inscripcion, m.status AS estado_matricula,
                               e.id_estudiante, p.ci AS ci_estudiante,
                               p.nombre AS nombre_estudiante, p.apellido AS apellido_estudiante
                        FROM matricula m
                        INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
                        INNER JOIN persona p ON e.id_persona = p.id_persona
                        WHERE m.id_matricula = ?";
                return $this->select($sql, [$idMatricula]);
            }
        }

        // ITERACIÓN 2: última matrícula del estudiante por CI (base de la rematriculación).
        public function ultimaMatriculaByCi(string $ci)
        {
            $sql = "SELECT m.id_matricula, m.gestion, m.id_paralelo, m.tipo AS tipo_matricula,
                           m.folio, m.estado_inscripcion, m.status AS estado_matricula,
                           e.id_estudiante, p.ci AS ci_estudiante,
                           p.nombre AS nombre_estudiante, p.apellido AS apellido_estudiante,
                           CONCAT(pa.nivel,' ',pa.grado,' \"',pa.sigla,'\"') AS curso
                    FROM matricula m
                    INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
                    INNER JOIN persona p ON e.id_persona = p.id_persona
                    LEFT JOIN paralelo pa ON pa.id_paralelo = m.id_paralelo
                    WHERE p.ci = ?
                    ORDER BY m.gestion DESC, m.id_matricula DESC LIMIT 1";
            return $this->select($sql, [$ci]);
        }

        // ITERACIÓN 1: marca documentación pendiente / completa sin tocar SP de pensiones.
        // No rompe si la migración aún no se aplicó: captura excepción y retorna true.
        public function setDocumentacion(int $idMatricula, ?string $plazoYmd, array $checklist, int $compromiso, string $obs = '', ?string $estado = null)
        {
            $json = function_exists('docsChecklistEncode') ? docsChecklistEncode($checklist) : json_encode($checklist);
            try {
                if ($estado !== null) {
                    $ok = $this->update(
                        "UPDATE matricula SET plazo_documentos_hasta = ?, docs_checklist = ?, compromiso_firmado = ?, docs_observacion = ?, estado_inscripcion = ? WHERE id_matricula = ?",
                        [$plazoYmd, $json, $compromiso ? 1 : 0, $obs !== '' ? $obs : null, $estado, $idMatricula]
                    );
                } else {
                    $ok = $this->update(
                        "UPDATE matricula SET plazo_documentos_hasta = ?, docs_checklist = ?, compromiso_firmado = ?, docs_observacion = ? WHERE id_matricula = ?",
                        [$plazoYmd, $json, $compromiso ? 1 : 0, $obs !== '' ? $obs : null, $idMatricula]
                    );
                }
                return $ok ? true : false;
            } catch (Exception $e) {
                // Columnas aún no migradas: no bloquear matrícula
                return true;
            }
        }

        public function setDocumentacionByCiGestion(string $ci, int $gestion, ?string $plazoYmd, array $checklist, int $compromiso, string $obs = '', ?string $estado = null)
        {
            try {
                $row = $this->select(
                    "SELECT m.id_matricula FROM matricula m INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante
                     INNER JOIN persona p ON e.id_persona=p.id_persona
                     WHERE p.ci=? AND m.gestion=? ORDER BY m.id_matricula DESC LIMIT 1",
                    [$ci, $gestion]
                );
                if (empty($row['id_matricula'])) { return false; }
                return $this->setDocumentacion((int)$row['id_matricula'], $plazoYmd, $checklist, $compromiso, $obs, $estado);
            } catch (Exception $e) { return false; }
        }

        // Actualizar matrícula existente (no regenera pensiones)
        // ITERACIÓN 2: motivo_estado opcional (M02, tolerante si la columna no existe).
        public function updateMatricula(int $idMatricula, int $idParalelo, string $tipo, string $folio, string $estadoInscripcion, string $motivo = '')
        {
            $motivo = trim($motivo);
            try {
                $sql = "UPDATE matricula SET id_paralelo = ?, tipo = ?, folio = ?, estado_inscripcion = ?, motivo_estado = ? WHERE id_matricula = ?";
                $ok = $this->update($sql, [$idParalelo, $tipo, $folio, $estadoInscripcion, $motivo !== '' ? $motivo : null, $idMatricula]);
            } catch (Exception $e) {
                $sql = "UPDATE matricula SET id_paralelo = ?, tipo = ?, folio = ?, estado_inscripcion = ? WHERE id_matricula = ?";
                $ok = $this->update($sql, [$idParalelo, $tipo, $folio, $estadoInscripcion, $idMatricula]);
            }
            return $ok ? "matricula_actualizada" : false;
        }

        // Baja lógica de matrícula
        public function deleteMatricula(int $idMatricula)
        {
            $sql = "UPDATE matricula SET status = 0 WHERE id_matricula = ?";
            return $this->update($sql, [$idMatricula]);
        }

    }


?>