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
        // I3: $userId fija created_by + id_user real (el SP deja 1). Tolerante.
        public function insertMatricula(string $strCi, int $intGestion, int $intIdParalelo, string $strTipoMatricula, string $strFolio, string $strStatus, ?int $userId = null)
        {
            try {
                $sql = "CALL matricular_estudiante_y_generar_pensiones(?,?,?,?,?,?)";
                $arrData = array($strCi, $intGestion, $intIdParalelo, $strTipoMatricula, $strFolio, $strStatus);
                $request_insert = $this->insert($sql, $arrData);
                if ($request_insert) {
                    if($userId !== null && $userId > 0){
                        try {
                            $this->update("UPDATE matricula SET created_by = ?, id_user = ? WHERE id_matricula = (
                                SELECT id_matricula FROM (SELECT m.id_matricula FROM matricula m
                                 INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
                                 INNER JOIN persona p ON e.id_persona = p.id_persona
                                 WHERE p.ci = ? AND m.gestion = ? ORDER BY m.id_matricula DESC LIMIT 1) t)",
                                [$userId, $userId, $strCi, $intGestion]);
                        } catch (Exception $x) {}
                    }
                    // M06: rastro de creación (best-effort)
                    try {
                        $rowId = $this->select(
                            "SELECT m.id_matricula FROM matricula m INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante
                             INNER JOIN persona p ON e.id_persona=p.id_persona
                             WHERE p.ci=? AND m.gestion=? ORDER BY m.id_matricula DESC LIMIT 1", [$strCi, $intGestion]);
                        if(!empty($rowId['id_matricula'])){
                            $this->logCambio((int)$rowId['id_matricula'], 'creacion',
                                ['gestion' => $intGestion, 'paralelo' => $intIdParalelo, 'tipo' => $strTipoMatricula, 'estado' => $strStatus], $userId);
                        }
                    } catch (Exception $x) {}
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
        // M06: registra quién entrega ($entregadoPor) y deja rastro en matricula_cambios.
        public function setDocumentacion(int $idMatricula, ?string $plazoYmd, array $checklist, int $compromiso, string $obs = '', ?string $estado = null, ?int $userId = null, string $entregadoPor = '')
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
                if($ok){
                    $tipo = ($estado === 'Pendiente_Documentos') ? 'docs_pendientes' : 'entrega_docs';
                    $det = ['checklist' => $checklist, 'compromiso' => $compromiso ? 1 : 0,
                        'obs' => $obs !== '' ? $obs : null, 'plazo' => $plazoYmd];
                    if(trim($entregadoPor) !== ''){ $det['entregado_por'] = trim($entregadoPor); }
                    $this->logCambio($idMatricula, $tipo, $det, $userId);
                }
                return $ok ? true : false;
            } catch (Exception $e) {
                // Columnas aún no migradas: no bloquear matrícula
                return true;
            }
        }

        public function setDocumentacionByCiGestion(string $ci, int $gestion, ?string $plazoYmd, array $checklist, int $compromiso, string $obs = '', ?string $estado = null, ?int $userId = null, string $entregadoPor = '')
        {
            try {
                $row = $this->select(
                    "SELECT m.id_matricula FROM matricula m INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante
                     INNER JOIN persona p ON e.id_persona=p.id_persona
                     WHERE p.ci=? AND m.gestion=? ORDER BY m.id_matricula DESC LIMIT 1",
                    [$ci, $gestion]
                );
                if (empty($row['id_matricula'])) { return false; }
                return $this->setDocumentacion((int)$row['id_matricula'], $plazoYmd, $checklist, $compromiso, $obs, $estado, $userId, $entregadoPor);
            } catch (Exception $e) { return false; }
        }

        // I2-addenda: nota de rectificación histórica (override fuera de gestión activa).
        // Reutiliza motivo_estado para que sea visible en tabla/ficha (tooltip).
        public function setMotivoByCiGestion(string $ci, int $gestion, string $motivo)
        {
            try {
                $ok = $this->update(
                    "UPDATE matricula SET motivo_estado = ? WHERE id_matricula = (
                        SELECT id_matricula FROM (SELECT m.id_matricula FROM matricula m
                         INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
                         INNER JOIN persona p ON e.id_persona = p.id_persona
                         WHERE p.ci = ? AND m.gestion = ? ORDER BY m.id_matricula DESC LIMIT 1) t)",
                    [$motivo, $ci, $gestion]
                );
                return $ok ? true : false;
            } catch (Exception $e) { return false; }
        }

        // Actualizar matrícula existente (no regenera pensiones)
        // ITERACIÓN 2: motivo_estado opcional (M02, tolerante si la columna no existe).
        // I3: $userId fija updated_by (tolerante).
        // REV-Mat: $motivoRect se anexa al motivo (rectificación en edición).
        // M06: $tipoMod etiqueta el cambio; $entregadoPor registra quién entrega docs.
        //      Si no viene etiqueta, se autodetecta (curso/estado/otro). Todo best-effort.
        public function updateMatricula(int $idMatricula, int $idParalelo, string $tipo, string $folio, string $estadoInscripcion, string $motivo = '', ?int $userId = null, string $motivoRect = '', ?string $tipoMod = null, string $entregadoPor = '')
        {
            $motivo = trim($motivo);
            $motivoRect = trim($motivoRect);
            if($motivoRect !== ''){
                $motivo = trim(($motivo !== '' ? $motivo.' — ' : '').'Rectificación histórica: '.$motivoRect);
            }
            $antes = null;
            try {
                $antes = $this->select("SELECT id_paralelo, tipo, folio, estado_inscripcion, motivo_estado FROM matricula WHERE id_matricula = ?", [$idMatricula]);
            } catch (Exception $e) {}
            try {
                $sql = "UPDATE matricula SET id_paralelo = ?, tipo = ?, folio = ?, estado_inscripcion = ?, motivo_estado = ? WHERE id_matricula = ?";
                $ok = $this->update($sql, [$idParalelo, $tipo, $folio, $estadoInscripcion, $motivo !== '' ? $motivo : null, $idMatricula]);
            } catch (Exception $e) {
                $sql = "UPDATE matricula SET id_paralelo = ?, tipo = ?, folio = ?, estado_inscripcion = ? WHERE id_matricula = ?";
                $ok = $this->update($sql, [$idParalelo, $tipo, $folio, $estadoInscripcion, $idMatricula]);
            }
            if($ok && $userId !== null && $userId > 0){
                try { $this->update("UPDATE matricula SET updated_by = ? WHERE id_matricula = ?", [$userId, $idMatricula]); } catch (Exception $x) {}
            }
            if($ok){
                $det = ['motivo' => $motivo !== '' ? $motivo : null];
                if(trim($entregadoPor) !== ''){ $det['entregado_por'] = trim($entregadoPor); }
                if(is_array($antes)){
                    $dif = [];
                    if(intval($antes['id_paralelo'] ?? 0) !== $idParalelo){ $dif['paralelo'] = ['antes' => intval($antes['id_paralelo'] ?? 0), 'despues' => $idParalelo]; }
                    if(trim((string)($antes['tipo'] ?? '')) !== trim($tipo)){ $dif['tipo'] = ['antes' => $antes['tipo'] ?? '', 'despues' => $tipo]; }
                    if(trim((string)($antes['folio'] ?? '')) !== trim($folio)){ $dif['folio'] = ['antes' => $antes['folio'] ?? '', 'despues' => $folio]; }
                    if(trim((string)($antes['estado_inscripcion'] ?? '')) !== trim($estadoInscripcion)){ $dif['estado'] = ['antes' => $antes['estado_inscripcion'] ?? '', 'despues' => $estadoInscripcion]; }
                    if($dif){ $det['cambios'] = $dif; }
                }
                $tipo = $tipoMod !== null && trim($tipoMod) !== '' ? trim($tipoMod)
                    : (isset($dif['paralelo']) ? 'cambio_curso'
                    : (isset($dif['estado']) ? 'cambio_estado' : 'correccion'));
                $this->logCambio($idMatricula, $tipo, $det, $userId);
            }
            return $ok ? "matricula_actualizada" : false;
        }

        // M06: bitácora de cambios de matrícula (best-effort, nunca bloquea).
        public function logCambio(int $idMatricula, string $tipo, $detalle = null, ?int $userId = null)
        {
            try {
                $det = is_string($detalle) ? $detalle : json_encode($detalle ?: [], JSON_UNESCAPED_UNICODE);
                return (bool)$this->insert(
                    "INSERT INTO matricula_cambios (id_matricula, tipo, detalle, id_usuario) VALUES (?, ?, ?, ?)",
                    [$idMatricula, $tipo, $det !== '' ? $det : null, ($userId !== null && $userId > 0) ? $userId : null]);
            } catch (Exception $e) { return false; }
        }

        // M06: historial de cambios con usuario (recientes primero).
        public function cambiosDe(int $idMatricula, int $limite = 20)
        {
            try {
                return $this->select_all(
                    "SELECT c.id_cambio, c.tipo, c.detalle, c.fecha_reg,
                            CONCAT(p.nombre,' ',p.apellido) AS usuario
                     FROM matricula_cambios c LEFT JOIN persona p ON p.id_persona = c.id_usuario
                     WHERE c.id_matricula = ? ORDER BY c.id_cambio DESC LIMIT ".intval($limite), [$idMatricula]);
            } catch (Exception $e) { return []; }
        }

        // Baja lógica de matrícula.
        // REV-Mat: bloqueada si tiene pensiones COBRADAS (integridad financiera);
        // si no, arrastra pensiones pendientes a status 0 para no dejar deuda
        // huérfana en fichas y morosidad. Todo en transacción.
        public function deleteMatricula(int $idMatricula)
        {
            try {
                $pag = $this->select(
                    "SELECT COUNT(*) AS c FROM pensiones WHERE id_matricula = ? AND estado_pago = 1 AND status = 1",
                    [$idMatricula]);
                if(!empty($pag["c"])){
                    return "has_pagos";
                }
                $this->beginTransaction();
                $this->update("UPDATE pensiones SET status = 0 WHERE id_matricula = ?", [$idMatricula]);
                $this->update("UPDATE matricula SET status = 0 WHERE id_matricula = ?", [$idMatricula]);
                $this->commit();
                return true;
            } catch (Exception $e) {
                try{ $this->rollback(); }catch(Exception $x){}
                return false;
            }
        }

    }


?>