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
        public function selectMatricula(int $idMatricula)
        {
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

        // Actualizar matrícula existente (no regenera pensiones)
        public function updateMatricula(int $idMatricula, int $idParalelo, string $tipo, string $folio, string $estadoInscripcion)
        {
            $sql = "UPDATE matricula SET id_paralelo = ?, tipo = ?, folio = ?, estado_inscripcion = ? WHERE id_matricula = ?";
            $ok = $this->update($sql, [$idParalelo, $tipo, $folio, $estadoInscripcion, $idMatricula]);
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