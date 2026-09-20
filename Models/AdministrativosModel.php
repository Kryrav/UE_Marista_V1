<?php
    class AdministrativosModel extends Mysql
    {
        public function __construct(){ parent::__construct(); }
        public function selectAdmins()
        {
            $sql = "SELECT p.id_persona, p.ci, p.nombre, p.apellido, p.cel, p.email, p.status, r.nombrerol
                    FROM persona p INNER JOIN rol r ON p.id_rol = r.idrol
                    WHERE p.id_rol IN (1,2,3,4,6) AND p.status != 0 ORDER BY p.apellido, p.nombre";
            return $this->select_all($sql);
        }
        public function selectAdmin(int $id){ return $this->select("SELECT * FROM persona WHERE id_persona=?", [$id]); }
        public function roles(){ return $this->select_all("SELECT idrol, nombrerol FROM rol WHERE idrol IN (1,2,3,4,6)"); }
        public function insertAdmin(array $d)
        {
            try{
                $id=$this->insert("INSERT INTO persona(ci,nombre,apellido,sexo,direccion_dom,cel,email,usuario,password,id_rol,status) VALUES(?,?,?,?,?,?,?,?,?,?,?)",
                [$d["ci"],$d["nombre"],$d["apellido"],$d["sexo"],$d["direccion"],$d["cel"],$d["email"],$d["usuario"],$d["password"],$d["id_rol"],$d["status"]]);
                return $id?"dato_guardado":false;
            }catch(Exception $e){ return (strpos($e->getMessage(),"Duplicate")!==false)?"exist":$e->getMessage(); }
        }
        public function updateAdmin(int $id, array $d)
        {
            $this->update("UPDATE persona SET ci=?,nombre=?,apellido=?,sexo=?,direccion_dom=?,cel=?,email=?,usuario=?,id_rol=?,status=? WHERE id_persona=?",
            [$d["ci"],$d["nombre"],$d["apellido"],$d["sexo"],$d["direccion"],$d["cel"],$d["email"],$d["usuario"],$d["id_rol"],$d["status"],$id]);
            if(!empty($d["password"])){ $this->update("UPDATE persona SET password=? WHERE id_persona=?", [$d["password"],$id]); }
            return "dato_guardado";
        }
        public function deleteAdmin(int $id){ if($id==1) return false; return $this->update("UPDATE persona SET status=0 WHERE id_persona=?", [$id]); }
    }
?>
