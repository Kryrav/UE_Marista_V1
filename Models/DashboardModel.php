<?php
    class DashboardModel extends Mysql
    {
        public function __construct(){ parent::__construct(); }
        public function stats()
        {
            $s = [];
            $g = $this->select("CALL get_gestion_activa()");
            $gestion = intval($g["gestion"] ?? date("Y"));
            $s["gestion_activa"] = $g;
            $s["estudiantes"] = $this->select("SELECT COUNT(*) c FROM estudiante WHERE status=1")["c"] ?? 0;
            $s["estudiantes_inactivos"] = $this->select("SELECT COUNT(*) c FROM estudiante WHERE status=2")["c"] ?? 0;
            $s["tutores"] = $this->select("SELECT COUNT(*) c FROM padre WHERE status=1")["c"] ?? 0;
            $s["matriculas_gestion"] = $this->select("SELECT COUNT(*) c FROM matricula WHERE gestion=$gestion AND status=1")["c"] ?? 0;
            $s["docentes"] = $this->select("SELECT COUNT(*) c FROM persona WHERE id_rol=5 AND status=1")["c"] ?? 0;
            $fin = $this->select(
                "SELECT ".\Services\FinanzasService::SQL_COB." AS cob,
                        ".\Services\FinanzasService::SQL_ADE." AS ade,
                        ".\Services\FinanzasService::SQL_VEN." AS ven,
                        SUM(p.estado_pago=1) AS npag, SUM(p.estado_pago=0) AS npen
                 FROM pensiones p INNER JOIN matricula m ON p.id_matricula=m.id_matricula
                 WHERE p.status=1 AND m.gestion=$gestion");
            $s["cobrado_gestion"] = $fin["cob"] ?? 0;
            $s["adeudado_gestion"] = $fin["ade"] ?? 0;
            $s["vencido_gestion"] = $fin["ven"] ?? 0;
            $s["pensiones_pagadas"] = $fin["npag"] ?? 0;
            $s["pensiones_pendientes"] = $fin["npen"] ?? 0;
            $mora = $this->select("SELECT COUNT(*) c, COALESCE(SUM(monto),0) t FROM pensiones WHERE status=1 AND estado_pago=0 AND fecha_vencimiento < CURDATE()");
            $s["mora_n"] = $mora["c"] ?? 0;
            $s["mora_bs"] = $mora["t"] ?? 0;
            // Serie mensual gestión activa
            $meses = ["Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre"];
            $s["serie_labels"] = array_map(function($m){ return substr($m,0,3); }, $meses);
            $s["serie_cob"] = []; $s["serie_ade"] = [];
            foreach($meses as $i => $mes){
                $row = $this->select(
                    "SELECT ".\Services\FinanzasService::SQL_COB." AS cob,
                            ".\Services\FinanzasService::SQL_ADE." AS ade
                     FROM pensiones p INNER JOIN matricula m ON p.id_matricula=m.id_matricula
                     WHERE p.status=1 AND m.gestion=$gestion AND p.mes_num=".($i+2));
                $s["serie_cob"][] = (float)($row["cob"] ?? 0);
                $s["serie_ade"][] = (float)($row["ade"] ?? 0);
            }
            // Ocupación por paralelo
            $s["ocupacion"] = $this->select_all("CALL sp_contar_inscritos_paralelo_por_gestion($gestion)") ?: [];
            // Últimos pagos
            $s["ultimos_pagos"] = $this->select_all(
                "SELECT pen.nro_recibo, pen.monto, pen.fecha_reg_pago, pen.tipo_pago,
                        CONCAT(per.nombre,' ',per.apellido) AS estudiante, pen.mes, m.gestion
                 FROM pensiones pen
                 INNER JOIN matricula m ON pen.id_matricula=m.id_matricula
                 INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante
                 INNER JOIN persona per ON e.id_persona=per.id_persona
                 WHERE pen.estado_pago=1 AND pen.status=1
                 ORDER BY pen.fecha_reg_pago DESC LIMIT 6") ?: [];
            // Top morosos por deuda
            $s["top_morosos"] = $this->select_all(
                "SELECT CONCAT(per.nombre,' ',per.apellido) AS estudiante, per.ci,
                        COUNT(*) AS cuotas, COALESCE(SUM(p.monto),0) AS deuda
                 FROM pensiones p
                 INNER JOIN matricula m ON p.id_matricula=m.id_matricula
                 INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante
                 INNER JOIN persona per ON e.id_persona=per.id_persona
                 WHERE p.status=1 AND p.estado_pago=0 AND p.fecha_vencimiento < CURDATE()
                 GROUP BY e.id_estudiante ORDER BY deuda DESC LIMIT 5") ?: [];
            // Alertas operativas
            $s["sin_tutores"] = $this->select(
                "SELECT COUNT(*) c FROM estudiante e WHERE e.status=1 AND NOT EXISTS
                 (SELECT 1 FROM padre pa WHERE pa.id_estudiante=e.id_estudiante AND pa.status!=0)")["c"] ?? 0;
            $s["sin_folio"] = $this->select("SELECT COUNT(*) c FROM estudiante WHERE status!=0 AND (folio_fisico IS NULL)")["c"] ?? 0;
            $s["sin_tutor_curso"] = $this->select("SELECT COUNT(*) c FROM paralelo WHERE status=1 AND (tutor IS NULL OR tutor='' OR tutor='Sin asignación')")["c"] ?? 0;
            return $s;
        }
    }
?>
