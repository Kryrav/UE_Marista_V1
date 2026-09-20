<?php
    class PensionesModel extends Mysql{
        //Propiedades de Pensiones
            
            private $intId_pensiones;
            private $id_matricula;
            private $dateFechaDePago;
            private $strTipoPago;
            private $strCodigo;
            private $strNombre;
            private $strApellido;
            private $strCi;
            private $strRelacion;
            private $intMonto;
            private $intEstadoPago;
            private $boolStatus;
            private $strMes;
            private $dateFechaRegistroPension;

        public function __construct()
        {
            parent::__construct();
        }	
        //Lista todas las materias ya registradas-------
        public function selectPensionesPagadas()
        {
            $sql = "CALL obtener_informacion_pensiones_pagadas();";
            $request = $this->select_all($sql);
            return $request;
            die();
        }

        public function selectPensionesEstudiantes(string $Ci)
        {
            try {
                $sql="CALL obtener_pensiones_estudiante(?)";
                return $this->select_all($sql, [$Ci]);
            } catch (Exception $e) {
                return "DB: " . $e->getMessage();
            }
        }

        public function setPago(int $txtMonto, string $listTipoPago, string $txtCodigo, string $txtNameAportante, string $txtLastAportante, string $txtCiAportante, string $txtParentesco, int $intIdPension, int $idUsuario = 0)
        {
            // Validación de monto exacto: no se altera la deuda (sin parciales)
            $actual = $this->select("SELECT monto, estado_pago FROM pensiones WHERE id_pensiones = ?", [$intIdPension]);
            if(empty($actual)){ return "La mensualidad no existe."; }
            if(intval($actual["estado_pago"]) === 1){ return "La mensualidad ya está pagada."; }
            if((float)$txtMonto !== (float)$actual["monto"]){
                return "El monto debe ser exacto: Bs. ".number_format((float)$actual["monto"], 2);
            }
            // Folio de recibo secuencial (UNIQUE como guarda; reintento ante carrera)
            for($t = 0; $t < 3; $t++){
                $nx = $this->select("SELECT COALESCE(MAX(nro_recibo),0)+1 AS nx FROM pensiones");
                $nro = (int)($nx["nx"] ?? 1);
                try {
                    $sql = "CALL pagarPension (?,?,?,?,?,?,?,?,?,?,?)";
                    $ok = $this->update($sql, [$intIdPension, $listTipoPago, $txtCodigo, $txtNameAportante,
                        $txtLastAportante, $txtCiAportante, $txtParentesco, $txtMonto, date("Y-m-d H:i:s"), $nro, $idUsuario]);
                    if ($ok) {
                        $this->insert("INSERT INTO pension_movimientos(id_pension, accion, detalle, id_usuario) VALUES(?,?,?,?)",
                            [$intIdPension, "pago", "$listTipoPago $txtCodigo - Bs. $txtMonto (Recibo N° $nro)", $idUsuario]);
                        return ["status" => "Pagado", "recibo" => $nro];
                    }
                    return "No se pudo registrar el pago.";
                } catch (Exception $e) {
                    if(strpos($e->getMessage(), "uq_nro_recibo") !== false){ continue; }
                    return "DB: " . $e->getMessage();
                }
            }
            return "No se pudo asignar folio de recibo.";
        }

        public function selectPensionDate(int $idPension)
        {
            try {
                $sql="CALL obtener_detalle_completo_pension(?)";
                return $this->select($sql, [$idPension]);
            } catch (Exception $e) {
                return "DB: " . $e->getMessage();
            }
        }

        // Anulación con motivo + auditoría (vuelve la mensualidad a pendiente)
        public function anularPension(int $idPension, string $motivo, int $idUsuario = 0)
        {
            $actual = $this->select("SELECT estado_pago FROM pensiones WHERE id_pensiones = ?", [$idPension]);
            if(empty($actual)){ return "No existe la mensualidad."; }
            if(intval($actual["estado_pago"]) === 0){ return "Ya está pendiente."; }
            $ok = $this->update(
                "UPDATE pensiones SET estado_pago = 0, tipo_pago = NULL, codigo = NULL, nombre = NULL, apellido = NULL, ci = NULL, relacion = NULL, fecha_reg_pago = NULL, nro_recibo = NULL, id_usuario = NULL WHERE id_pensiones = ?",
                [$idPension]);
            if($ok){
                $this->insert("INSERT INTO pension_movimientos(id_pension, accion, detalle, id_usuario) VALUES(?,?,?,?)",
                    [$idPension, "anulacion", $motivo, $idUsuario]);
                return "Anulado";
            }
            return "No se pudo anular.";
        }

        public function getColegio(int $colegio)
        {
            $sql="CALL obtener_colegio_por_id(?)";
            return $this->select($sql, [$colegio]);
        }

        // Morosidad: pendientes con vencimiento pasado, más atrasadas primero
        public function morosidad()
        {
            $sql = "SELECT p.id_pensiones, p.mes, p.monto, p.fecha_vencimiento,
                           DATEDIFF(CURDATE(), p.fecha_vencimiento) AS dias_atraso,
                           m.id_matricula, m.gestion,
                           CONCAT(pa.nivel, ': ', pa.grado, ' - ', pa.sigla) AS curso,
                           per.ci AS ci_estudiante,
                           CONCAT(per.nombre, ' ', per.apellido) AS estudiante
                    FROM pensiones p
                    INNER JOIN matricula m ON p.id_matricula = m.id_matricula
                    INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
                    INNER JOIN persona per ON e.id_persona = per.id_persona
                    INNER JOIN paralelo pa ON m.id_paralelo = pa.id_paralelo
                    WHERE p.status = 1 AND p.estado_pago = 0 AND p.fecha_vencimiento < CURDATE()
                    ORDER BY p.fecha_vencimiento ASC";
            return $this->select_all($sql);
        }

        // Serie mensual cobrado por mes (gestión activa, orden calendario) para el gráfico
        public function serieMensual()
        {
            $g = $this->select("SELECT gestion FROM gestion WHERE status = 1 LIMIT 1");
            $gestion = $g["gestion"] ?? null;
            $meses = ["Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre"];
            $out = ["gestion" => $gestion, "labels" => [], "cobrado" => [], "adeudado" => []];
            if(!$gestion){ return $out; }
            foreach($meses as $i => $mes){
                $out["labels"][] = substr($mes, 0, 3);
                $row = $this->select(
                    "SELECT COALESCE(SUM(CASE WHEN p.estado_pago=1 THEN p.monto ELSE 0 END),0) AS cob,
                            COALESCE(SUM(CASE WHEN p.estado_pago=0 THEN p.monto ELSE 0 END),0) AS ade
                     FROM pensiones p INNER JOIN matricula m ON p.id_matricula = m.id_matricula
                     WHERE p.status = 1 AND m.gestion = ? AND p.mes_num = ?", [$gestion, $i + 2]);
                $out["cobrado"][] = (float)($row["cob"] ?? 0);
                $out["adeudado"][] = (float)($row["ade"] ?? 0);
            }
            return $out;
        }

        // KPIs para la cabecera: cobrado, adeudado y vencido (gestión activa + global)
        public function resumen()
        {
            $g = $this->select("SELECT gestion FROM gestion WHERE status = 1 LIMIT 1");
            $gestion = $g["gestion"] ?? null;
            $r = ["gestion_activa" => $gestion, "cobrado" => 0, "adeudado" => 0,
                  "vencido_bs" => 0, "vencidas" => 0, "cobrado_gestion" => 0, "adeudado_gestion" => 0,
                  "vencido_gestion" => 0];
            $row = $this->select(
                "SELECT COALESCE(SUM(CASE WHEN estado_pago=1 THEN monto ELSE 0 END),0) AS cob,
                        COALESCE(SUM(CASE WHEN estado_pago=0 THEN monto ELSE 0 END),0) AS ade
                 FROM pensiones WHERE status = 1");
            if($row){ $r["cobrado"] = $row["cob"]; $r["adeudado"] = $row["ade"]; }
            $row = $this->select(
                "SELECT COUNT(*) AS n, COALESCE(SUM(monto),0) AS m FROM pensiones
                 WHERE status = 1 AND estado_pago = 0 AND fecha_vencimiento < CURDATE()");
            if($row){ $r["vencidas"] = $row["n"]; $r["vencido_bs"] = $row["m"]; }
            if($gestion){
                $row = $this->select(
                    "SELECT COALESCE(SUM(CASE WHEN p.estado_pago=1 THEN p.monto ELSE 0 END),0) AS cob,
                            COALESCE(SUM(CASE WHEN p.estado_pago=0 THEN p.monto ELSE 0 END),0) AS ade,
                            COALESCE(SUM(CASE WHEN p.estado_pago=0 AND p.fecha_vencimiento < CURDATE() THEN p.monto ELSE 0 END),0) AS ven
                     FROM pensiones p INNER JOIN matricula m ON p.id_matricula = m.id_matricula
                     WHERE p.status = 1 AND m.gestion = ?", [$gestion]);
                if($row){ $r["cobrado_gestion"] = $row["cob"]; $r["adeudado_gestion"] = $row["ade"]; $r["vencido_gestion"] = $row["ven"]; }
            }
            return $r;
        }

        // Verificación pública de un recibo por su folio (para el QR)
        public function verificarRecibo(int $nro)
        {
            $sql = "SELECT p.nro_recibo, p.mes, p.monto, p.estado_pago, p.tipo_pago, p.fecha_reg_pago,
                           m.gestion, CONCAT(pa.nivel,' ',pa.grado,' \"',pa.sigla,'\"') AS curso,
                           CONCAT(per.nombre,' ',per.apellido) AS estudiante, per.ci AS ci_estudiante,
                           col.nombre_col AS colegio
                    FROM pensiones p
                    INNER JOIN matricula m ON p.id_matricula = m.id_matricula
                    INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
                    INNER JOIN persona per ON e.id_persona = per.id_persona
                    LEFT JOIN paralelo pa ON pa.id_paralelo = m.id_paralelo
                    LEFT JOIN colegio col ON col.id_colegio = 1
                    WHERE p.nro_recibo = ? AND p.status = 1";
            return $this->select($sql, [$nro]);
        }
    }
    
?>