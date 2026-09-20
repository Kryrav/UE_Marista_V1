<?php
    class DocentesModel extends Mysql
    {
        public function __construct(){ parent::__construct(); }
        public function selectDocentes()
        {
            $sql = "SELECT p.id_persona, p.ci, p.nombre, p.apellido, p.cel, p.email, p.status, r.nombrerol
                    FROM persona p INNER JOIN rol r ON p.id_rol = r.idrol
                    WHERE p.id_rol = 5 AND p.status != 0 ORDER BY p.apellido, p.nombre";
            return $this->select_all($sql);
        }
        public function selectDocente(int $id)
        {
            $sql = "SELECT * FROM persona WHERE id_persona = ?";
            return $this->select($sql, [$id]);
        }
        public function insertDocente(array $d)
        {
            try{
                $sql = "INSERT INTO persona(ci,nombre,apellido,sexo,direccion_dom,cel,email,usuario,password,id_rol,status) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
                $id = $this->insert($sql, [$d["ci"],$d["nombre"],$d["apellido"],$d["sexo"],$d["direccion"],$d["cel"],$d["email"],$d["usuario"],$d["password"],5,$d["status"]]);
                return $id ? "dato_guardado" : false;
            }catch(Exception $e){ $m=$e->getMessage(); return (strpos($m,"Duplicate")!==false)?"exist":$m; }
        }
        public function updateDocente(int $id, array $d)
        {
            $sql = "UPDATE persona SET ci=?,nombre=?,apellido=?,sexo=?,direccion_dom=?,cel=?,email=?,usuario=?,status=? WHERE id_persona=?";
            $this->update($sql, [$d["ci"],$d["nombre"],$d["apellido"],$d["sexo"],$d["direccion"],$d["cel"],$d["email"],$d["usuario"],$d["status"],$id]);
            if(!empty($d["password"])){ $this->update("UPDATE persona SET password=? WHERE id_persona=?", [$d["password"],$id]); }
            return "dato_guardado";
        }
        public function deleteDocente(int $id){ return $this->update("UPDATE persona SET status=0 WHERE id_persona=?", [$id]); }

        // Docentes activos para selector de tutor de curso (guía debe ser docente)
        public function optionsActivos()
        {
            return $this->select_all(
                "SELECT id_persona, CONCAT(nombre,' ',apellido) AS nombre_completo
                 FROM persona WHERE id_rol = 5 AND status = 1 ORDER BY apellido, nombre");
        }

        // Resuelve y valida: el id debe ser docente activo; 0/vacío = Sin asignación
        public function nombreTutor(int $idPersona)
        {
            if($idPersona <= 0){ return "Sin asignación"; }
            $r = $this->select(
                "SELECT CONCAT(nombre,' ',apellido) AS n FROM persona WHERE id_persona = ? AND id_rol = 5 AND status = 1",
                [$idPersona]);
            return $r ? $r["n"] : null;
        }
    }
?>
