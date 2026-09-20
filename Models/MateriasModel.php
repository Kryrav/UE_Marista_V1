<?php 

	class MateriasModel extends Mysql
	{
        //Propiedades de materias
            
            private $intIdMateria;
            private $strArea;
            private $strNombre;
            private $strDescripcion;
            private $intGrado;
            private $strNivel;
            private $intHoras;
            private $intStatus;

		public function __construct()
		{
			parent::__construct();
		}	
        //Lista todas las materias ya registradas----------------------------------------------------------------------------------------------------------------------------------------------------------------------
        public function selectMaterias()
        {
            $sql = "select * from materia where status > 0 ";
            $request = $this->select_all($sql);
            return $request;
            die();
        }

        //Insertar nueva materia-------------------------------------------------------------------------------------------------------------------------------------------------------------------
        public function insertMateria(string $Area,string $Nombre,string $Descripcion, int $Grado,string $Nivel,int $Horas,int $status){
            //Tomamos los datos recibidos
            $this->intIdMateria  =   null;

            $this->strArea  =        $Area;
            $this->strNombre=        $Nombre;
            $this->strDescripcion=   $Descripcion;
            $this->intGrado =        $Grado;
            $this->strNivel =        $Nivel;
            $this->intHoras =        $Horas;
            $this->intStatus=        $status;

            //Configuramos la respuesta a mostrar
            $request_insert="";
            try {
                //Preparamos la consulta SQL
                $sql="CALL sp_insertar_materia (?,?,?,?,?,?,?)";
                $arrData    =   array($this->strArea  , $this->strNombre, $this->strDescripcion,  $this->intGrado , $this->strNivel,  $this->intHoras,  $this->intStatus   );   
                $request_insert=$this->insert($sql, $arrData);
                if ($request_insert) {
                    return "dato_guardado";
                }else {
                    return $request_insert;
                }
                dep($request_insert);
            } catch (PDOException $e) {
                // Capturamos errores específicos del procedimiento almacenado
                if (strpos($e->getMessage(), 'La materia ya existe en este grado') !== false) {
                    return "exist"; // Materia ya existe
                } else {
                    // Manejo de otro tipo de error
                    return "DB: " . $e->getMessage();
                }
            }
            die();
        }

        //Consultar datos de 1 materia ----------------------------------------------------------------------------------------------------------------------------------------------------------------------
        public function selectMateria($IdMat){
            $this->intIdMateria = intval($IdMat);
			$sql = "SELECT id_materia, area_mat, nombre_mat, descripcion_mat, grado, nivel, horas_mat, status, DATE_FORMAT(fecha_reg, '%d-%m-%Y') AS fechaRegistro 
                        FROM materia WHERE id_materia = ?";
			return $this->select($sql, [$this->intIdMateria]);

        }
        //Eliminar 1 materia ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

        public function deleteMateria(int $IdMateria){
            $this->intIdMateria =   $IdMateria;
            $sql=   "UPDATE materia SET status = 0 WHERE id_materia = ?"; //Preparamos sentencia
            $arrData=array($this->intIdMateria);    //Preparamos los datos a llenar con ?
            $request=$this->update($sql,$arrData);   //Ejecutamos la sentencia y guardamos 
            return $request;

        }

        public function updateMateria(int $IdMateria, string $Area,string $Nombre,string $Descripcion,int $Grado,string $Nivel,int $Horas,int $status){
            //  Tomamos los datos
            $this->intIdMateria  =   $IdMateria;//  1
            $this->strArea  =        $Area;     //  2
            $this->strNombre=        $Nombre;   //  3
            $this->strDescripcion=   $Descripcion;//4
            $this->intGrado =        $Grado;    //  5
            $this->strNivel =        $Nivel;    //  6
            $this->intHoras =        $Horas;    //  7
            $this->intStatus=        $status;   //  8
            $request_insert="";
            try {
                //  llamamos al procedimiento almacenado 
                $sql="CALL sp_actualizar_materia(?,?,?,?,?,?,?,?)";
                $arrData=array(
                    $this->intIdMateria,
                    $this->strArea ,
                    $this->strNombre,
                    $this->strDescripcion,
                    $this->intGrado,
                    $this->strNivel ,
                    $this->intHoras ,
                    $this->intStatus
                );
                $request_insert =   $this->update($sql, $arrData);
                if ($request_insert) {
                    return "dato_guardado";
                }
                return "Error al actualizar";
            } catch (PDOException $e) {
                // Capturamos errores específicos del procedimiento almacenado
                if (strpos($e->getMessage(), 'Ya existe una materia con el mismo nombre y grado') !== false) {
                    return "exist"; // Materia ya existe
                } else {
                    // Manejo de otro tipo de error
                    return "DB: " . $e->getMessage();
                }
            }
        }
	}
 ?>



