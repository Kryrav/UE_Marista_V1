<?php
    class ReportesModel extends Mysql
    {
        public function __construct(){ parent::__construct(); }

        public function gestiones()
        {
            return $this->select_all("SELECT gestion, monto_pension, status FROM gestion ORDER BY gestion DESC");
        }

        private function g($gestion)
        {
            if($gestion){ return intval($gestion); }
            $r = $this->select("SELECT gestion FROM gestion WHERE status = 1 LIMIT 1");
            return intval($r["gestion"] ?? date("Y"));
        }

        // ---- Financieros ----
        public function resumenFinanciero($gestion = 0)
        {
            $g = $this->g($gestion);
            $r = $this->select(
                "SELECT COUNT(DISTINCT m.id_matricula) AS matriculas,
                        COUNT(*) AS cuotas,
                        SUM(p.estado_pago=1) AS pagadas,
                        SUM(p.estado_pago=0) AS pendientes,
                        ".\Services\FinanzasService::SQL_COB." AS cobrado,
                        ".\Services\FinanzasService::SQL_ADE." AS adeudado,
                        ".\Services\FinanzasService::SQL_VEN." AS vencido,
                        SUM(p.estado_pago=0 AND p.fecha_vencimiento < CURDATE()) AS vencidas
                 FROM pensiones p INNER JOIN matricula m ON p.id_matricula = m.id_matricula
                 WHERE p.status = 1 AND m.gestion = ?", [$g]);
            $r["gestion"] = $g;
            $r["pct_cobro"] = \Services\FinanzasService::pctCobro($r["cobrado"] ?? 0, $r["adeudado"] ?? 0);
            return $r;
        }

        public function mensual($gestion = 0)
        {
            $g = $this->g($gestion);
            $meses = ["Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre"];
            $out = [];
            foreach($meses as $i => $mes){
                $r = $this->select(
                    "SELECT COUNT(*) AS cuotas, SUM(p.estado_pago=1) AS pagadas, SUM(p.estado_pago=0) AS pendientes,
                            ".\Services\FinanzasService::SQL_COB." AS cobrado,
                            ".\Services\FinanzasService::SQL_ADE." AS adeudado,
                            ".\Services\FinanzasService::SQL_VEN." AS vencido
                     FROM pensiones p INNER JOIN matricula m ON p.id_matricula = m.id_matricula
                     WHERE p.status = 1 AND m.gestion = ? AND p.mes_num = ?", [$g, $i + 2]);
                $r["mes"] = $mes; $r["gestion"] = $g;
                $out[] = $r;
            }
            return $out;
        }

        public function anual()
        {
            return $this->select_all(
                "SELECT m.gestion, COUNT(DISTINCT m.id_matricula) AS matriculas, COUNT(*) AS cuotas,
                        SUM(p.estado_pago=1) AS pagadas, SUM(p.estado_pago=0) AS pendientes,
                        ".\Services\FinanzasService::SQL_COB." AS cobrado,
                        ".\Services\FinanzasService::SQL_ADE." AS adeudado,
                        ".\Services\FinanzasService::SQL_VEN." AS vencido
                 FROM pensiones p INNER JOIN matricula m ON p.id_matricula = m.id_matricula
                 WHERE p.status = 1 GROUP BY m.gestion ORDER BY m.gestion");
        }

        public function porCurso($gestion = 0)
        {
            $g = $this->g($gestion);
            return $this->select_all(
                "SELECT CONCAT(pa.nivel,' ',pa.grado,' \"',pa.sigla,'\"') AS curso, pa.tutor,
                        COUNT(DISTINCT m.id_matricula) AS matriculas, COUNT(*) AS cuotas,
                        SUM(p.estado_pago=1) AS pagadas, SUM(p.estado_pago=0) AS pendientes,
                        ".\Services\FinanzasService::SQL_COB." AS cobrado,
                        ".\Services\FinanzasService::SQL_ADE." AS adeudado
                 FROM pensiones p
                 INNER JOIN matricula m ON p.id_matricula = m.id_matricula
                 INNER JOIN paralelo pa ON pa.id_paralelo = m.id_paralelo
                 WHERE p.status = 1 AND m.gestion = ?
                 GROUP BY pa.id_paralelo ORDER BY pa.nivel DESC, pa.grado", [$g]);
        }

        public function porCajero($gestion = 0)
        {
            $g = $this->g($gestion);
            return $this->select_all(
                "SELECT CONCAT(uc.nombre,' ',uc.apellido) AS cajero,
                        COUNT(*) AS cobros, COALESCE(SUM(p.monto),0) AS total
                 FROM pension_movimientos mv
                 INNER JOIN pensiones p ON p.id_pensiones = mv.id_pension
                 INNER JOIN matricula m ON p.id_matricula = m.id_matricula
                 LEFT JOIN persona uc ON uc.id_persona = mv.id_usuario
                 WHERE mv.accion = 'pago' AND m.gestion = ?
                 GROUP BY mv.id_usuario ORDER BY total DESC", [$g]);
        }

        // ---- Académicos / estadística ----
        public function matriculaStats($gestion = 0)
        {
            $g = $this->g($gestion);
            return [
                "gestion" => $g,
                "por_paralelo" => $this->select_all(
                    "SELECT CONCAT(pa.nivel,' ',pa.grado,' \"',pa.sigla,'\"') AS curso, pa.cupo, pa.tutor,
                            COUNT(m.id_matricula) AS inscritos
                     FROM paralelo pa LEFT JOIN matricula m ON m.id_paralelo = pa.id_paralelo AND m.gestion = ? AND m.status = 1
                     WHERE pa.status = 1 GROUP BY pa.id_paralelo ORDER BY pa.nivel DESC, pa.grado", [$g]),
                "por_tipo" => $this->select_all(
                    "SELECT tipo, COUNT(*) AS c FROM matricula WHERE gestion = ? AND status = 1 GROUP BY tipo", [$g]),
                "por_sexo" => $this->select_all(
                    "SELECT per.sexo, COUNT(*) AS c FROM matricula m
                     INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
                     INNER JOIN persona per ON e.id_persona = per.id_persona
                     WHERE m.gestion = ? AND m.status = 1 GROUP BY per.sexo", [$g]),
            ];
        }

        public function estudiantesStats()
        {
            return [
                "por_estado" => $this->select_all(
                    "SELECT status, COUNT(*) AS c FROM estudiante GROUP BY status"),
                "por_sexo" => $this->select_all(
                    "SELECT per.sexo, COUNT(*) AS c FROM estudiante e
                     INNER JOIN persona per ON e.id_persona = per.id_persona
                     WHERE e.status != 0 GROUP BY per.sexo"),
                "por_registro" => $this->select_all(
                    "SELECT estado_reg, COUNT(*) AS c FROM estudiante WHERE status != 0 GROUP BY estado_reg"),
                "sin_tutores" => $this->select_all(
                    "SELECT CONCAT(per.nombre,' ',per.apellido) AS estudiante, per.ci
                     FROM estudiante e INNER JOIN persona per ON e.id_persona = per.id_persona
                     WHERE e.status = 1 AND NOT EXISTS
                     (SELECT 1 FROM padre pa WHERE pa.id_estudiante = e.id_estudiante AND pa.status != 0)
                     ORDER BY per.apellido LIMIT 100"),
                "sin_folio" => $this->select(
                    "SELECT COUNT(*) AS c FROM estudiante WHERE status != 0 AND folio_fisico IS NULL")["c"] ?? 0,
            ];
        }
    }
?>
