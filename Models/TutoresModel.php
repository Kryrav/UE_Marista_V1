<?php
    class TutoresModel extends Mysql
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function selectTutores()
        {
            $sql = "SELECT pa.id_padre, pa.tipo_parentesco, pa.profesion, pa.status AS status_padre,
                           t.id_persona AS id_persona_tutor, t.ci AS ci_tutor, t.nombre AS nombre_tutor,
                           t.apellido AS apellido_tutor, t.cel AS cel_tutor, t.email AS email_tutor,
                           e.id_estudiante, pe.ci AS ci_estudiante,
                           CONCAT(pe.nombre,' ',pe.apellido) AS estudiante
                    FROM padre pa
                    INNER JOIN persona t ON pa.id_persona = t.id_persona
                    INNER JOIN estudiante e ON pa.id_estudiante = e.id_estudiante
                    INNER JOIN persona pe ON e.id_persona = pe.id_persona
                    WHERE pa.status != 0
                    ORDER BY t.apellido, t.nombre";
            return $this->select_all($sql);
        }

        public function selectTutor(int $idPadre)
        {
            $sql = "SELECT pa.*, t.ci, t.nombre, t.apellido, t.sexo, t.cel, t.email, t.direccion_dom,
                           t.usuario, t.status AS status_persona, e.id_estudiante,
                           pe.ci AS ci_estudiante, CONCAT(pe.nombre,' ',pe.apellido) AS estudiante
                    FROM padre pa
                    INNER JOIN persona t ON pa.id_persona = t.id_persona
                    INNER JOIN estudiante e ON pa.id_estudiante = e.id_estudiante
                    INNER JOIN persona pe ON e.id_persona = pe.id_persona
                    WHERE pa.id_padre = ?";
            return $this->select($sql, [$idPadre]);
        }

        public function selectByEstudiante(int $idEstudiante)
        {
            $sql = "SELECT pa.id_padre, pa.tipo_parentesco, pa.nacionalidad, pa.estado_civil,
                           pa.profesion, pa.empresa_trabajo, pa.observaciones,
                           t.ci, t.nombre, t.apellido, t.sexo, t.direccion_dom, t.cel, t.email
                    FROM padre pa
                    INNER JOIN persona t ON pa.id_persona = t.id_persona
                    WHERE pa.id_estudiante = ? AND pa.status != 0";
            return $this->select_all($sql, [$idEstudiante]);
        }

        public function listEstudiantesOptions()
        {
            $sql = "SELECT e.id_estudiante, CONCAT(p.ci,' - ',p.nombre,' ',p.apellido) AS label
                    FROM estudiante e INNER JOIN persona p ON e.id_persona = p.id_persona
                    WHERE e.status = 1 ORDER BY p.apellido, p.nombre LIMIT 500";
            return $this->select_all($sql);
        }

        // Buscador para autocomplete: CI/RUDE/nombre/apellido, top 8, con contexto
        public function buscarEstudiantes(string $q)
        {
            $like = "%".$q."%";
            $sql = "SELECT e.id_estudiante, p.ci, e.rude, p.nombre, p.apellido,
                           CONCAT(pa.nivel,' ',pa.grado,' \"',pa.sigla,'\"') AS curso,
                           (SELECT COUNT(*) FROM padre WHERE id_estudiante = e.id_estudiante AND status != 0) AS tutores
                    FROM estudiante e
                    INNER JOIN persona p ON e.id_persona = p.id_persona
                    LEFT JOIN matricula m ON m.id_estudiante = e.id_estudiante AND m.status = 1
                        AND m.gestion = (SELECT gestion FROM gestion WHERE status = 1 LIMIT 1)
                    LEFT JOIN paralelo pa ON pa.id_paralelo = m.id_paralelo
                    WHERE e.status = 1 AND (p.ci LIKE ? OR e.rude LIKE ? OR p.nombre LIKE ? OR p.apellido LIKE ?)
                    ORDER BY p.apellido, p.nombre LIMIT 8";
            return $this->select_all($sql, [$like, $like, $like, $like]);
        }

        public function findPersonaByCi(string $ci)
        {
            $sql = "SELECT id_persona, ci, nombre, apellido, sexo, direccion_dom, cel, email, id_rol, status
                    FROM persona WHERE ci = ?";
            return $this->select($sql, [$ci]);
        }

        public function existeVinculo(int $idPersona, int $idEstudiante)
        {
            $r = $this->select(
                "SELECT id_padre FROM padre WHERE id_persona = ? AND id_estudiante = ? AND status != 0",
                [$idPersona, $idEstudiante]);
            return !empty($r);
        }

        public function insertTutor(array $tutor, int $idEstudiante, array $padre)
        {
            try {
                $this->beginTransaction();
                // Si la persona ya existe como tutor (ej. apoderado de otro hijo),
                // se reutiliza: solo se crea el vínculo, no se duplica la persona.
                $ex = $this->select("SELECT id_persona, id_rol FROM persona WHERE ci = ?", [$tutor["ci"]]);
                if(!empty($ex)){
                    if(intval($ex["id_rol"]) !== 14){
                        $this->rollback();
                        return "exist:El CI pertenece a otro usuario del sistema (no es tutor).";
                    }
                    $idPersona = $ex["id_persona"];
                    if($this->existeVinculo((int)$idPersona, $idEstudiante)){
                        $this->rollback();
                        return "exist:Este tutor ya está vinculado al estudiante.";
                    }
                    // Refresca datos de contacto con lo digitado
                    $this->update("UPDATE persona SET nombre=?, apellido=?, sexo=?, direccion_dom=?, cel=?, email=?, status=? WHERE id_persona=?",
                        [$tutor["nombre"],$tutor["apellido"],$tutor["sexo"],$tutor["direccion"],$tutor["cel"],$tutor["email"],$tutor["status"],$idPersona]);
                }else{
                    $sqlP = "INSERT INTO persona(ci, nombre, apellido, sexo, direccion_dom, cel, email, usuario, password, id_rol, status)
                             VALUES(?,?,?,?,?,?,?,?,?,?,?)";
                    $idPersona = $this->insert($sqlP, [
                        $tutor["ci"], $tutor["nombre"], $tutor["apellido"], $tutor["sexo"],
                        $tutor["direccion"], $tutor["cel"], $tutor["email"], $tutor["usuario"],
                        $tutor["password"], 14, $tutor["status"]
                    ]);
                    if(!$idPersona){ $this->rollback(); return "Error al crear persona"; }
                }
                $sqlH = "INSERT INTO padre(id_persona, id_estudiante, tipo_parentesco, nacionalidad, estado_civil, profesion, empresa_trabajo, observaciones, status)
                         VALUES(?,?,?,?,?,?,?,?,?)";
                $ok = $this->insert($sqlH, [
                    $idPersona, $idEstudiante, $padre["parentesco"], $padre["nacionalidad"],
                    $padre["estado_civil"], $padre["profesion"], $padre["empresa"],
                    $padre["observaciones"], 1
                ]);
                if(!$ok){ $this->rollback(); return "Error al vincular tutor"; }
                $this->commit();
                return "dato_guardado";
            } catch (Exception $e) {
                try{ $this->rollback(); }catch(Exception $x){}
                $msg = $e->getMessage();
                if(strpos($msg,"Duplicate")!==false) return "exist";
                return $msg;
            }
        }

        public function updateTutor(int $idPadre, array $tutor, int $idEstudiante, array $padre)
        {
            try {
                $cur = $this->select("SELECT id_persona FROM padre WHERE id_padre = ?", [$idPadre]);
                if(empty($cur)) return "No existe el tutor";
                $idPersona = $cur["id_persona"];
                $this->beginTransaction();
                $sqlP = "UPDATE persona SET ci=?, nombre=?, apellido=?, sexo=?, direccion_dom=?, cel=?, email=?, usuario=?, status=? WHERE id_persona=?";
                $this->update($sqlP, [$tutor["ci"],$tutor["nombre"],$tutor["apellido"],$tutor["sexo"],$tutor["direccion"],$tutor["cel"],$tutor["email"],$tutor["usuario"],$tutor["status"],$idPersona]);
                if(!empty($tutor["password"])){
                    $this->update("UPDATE persona SET password=? WHERE id_persona=?", [$tutor["password"], $idPersona]);
                }
                $sqlH = "UPDATE padre SET id_estudiante=?, tipo_parentesco=?, nacionalidad=?, estado_civil=?, profesion=?, empresa_trabajo=?, observaciones=? WHERE id_padre=?";
                $this->update($sqlH, [$idEstudiante,$padre["parentesco"],$padre["nacionalidad"],$padre["estado_civil"],$padre["profesion"],$padre["empresa"],$padre["observaciones"],$idPadre]);
                $this->commit();
                return "dato_guardado";
            } catch (Exception $e) {
                try{ $this->rollback(); }catch(Exception $x){}
                return $e->getMessage();
            }
        }

        public function deleteTutor(int $idPadre)
        {
            return $this->update("UPDATE padre SET status = 0 WHERE id_padre = ?", [$idPadre]);
        }
    }
?>
