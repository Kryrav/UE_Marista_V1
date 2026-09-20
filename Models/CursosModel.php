<?php
class CursosModel extends Mysql{
    public function __construct()
		{
			parent::__construct();
		}

    public function selectCursos(int $intGestion){
        $sql = "CALL sp_contar_inscritos_paralelo_por_gestion(?)";
        return $this->select_all($sql, [$intGestion]);
    }
    public function selectdatosCurso(int $paralelo, int $gestion)
    {
        $sql = "CALL paralelo_por_gestion(?,?)";
        return $this->select_all($sql, [$paralelo, $gestion]);
    }

    public function selectEstudiantesCurso(int $paralelo, int $gestion)
    {
        $sql = "CALL listar_estudiantes_curso(?,?)";
        return $this->select_all($sql, [$paralelo, $gestion]);
    }

    // Nómina imprimible: curso + estudiantes con RUDE y sexo, orden alfabético
    public function selectListaImprimir(int $paralelo, int $gestion)
    {
        $curso = $this->select(
            "SELECT p.id_paralelo, p.nivel, p.grado, p.sigla, p.turno, p.tutor, p.cupo,
                    (SELECT COUNT(*) FROM matricula m WHERE m.id_paralelo = p.id_paralelo AND m.gestion = ? AND m.status = 1) AS total_inscritos
             FROM paralelo p WHERE p.id_paralelo = ?", [$gestion, $paralelo]);
        $est = $this->select_all(
            "SELECT per.ci, e.rude, per.nombre, per.apellido, per.sexo
             FROM matricula m
             INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
             INNER JOIN persona per ON e.id_persona = per.id_persona
             WHERE m.id_paralelo = ? AND m.gestion = ? AND m.status = 1 AND e.status != 0
             ORDER BY per.apellido, per.nombre", [$paralelo, $gestion]);
        return ["curso" => $curso, "estudiantes" => $est ?: []];
    }

    public function selectParalelo(int $id)
    {
        $sql = "SELECT p.*,
                       (SELECT d.id_persona FROM persona d
                        WHERE d.id_rol = 5 AND d.status = 1
                          AND CONCAT(d.nombre,' ',d.apellido) = p.tutor LIMIT 1) AS id_persona_tutor
                FROM paralelo p WHERE p.id_paralelo = ?";
        return $this->select($sql, [$id]);
    }

    public function insertParalelo(string $nivel, int $grado, string $sigla, int $cupo, string $tutor, string $turno, int $status)
    {
        $sql = "INSERT INTO paralelo(nivel, grado, sigla, cupo, tutor, turno, status) VALUES(?,?,?,?,?,?,?)";
        $id = $this->insert($sql, [$nivel, $grado, $sigla, $cupo, $tutor, $turno, $status]);
        return $id ? "dato_guardado" : false;
    }

    public function updateParalelo(int $id, string $nivel, int $grado, string $sigla, int $cupo, string $tutor, string $turno, int $status)
    {
        $sql = "UPDATE paralelo SET nivel=?, grado=?, sigla=?, cupo=?, tutor=?, turno=?, status=? WHERE id_paralelo=?";
        $ok = $this->update($sql, [$nivel, $grado, $sigla, $cupo, $tutor, $turno, $status, $id]);
        return $ok ? "dato_guardado" : false;
    }

    public function deleteParalelo(int $id)
    {
        $sql = "UPDATE paralelo SET status = 0 WHERE id_paralelo = ?";
        return $this->update($sql, [$id]);
    }


}
?>