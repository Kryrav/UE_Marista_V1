<?php
    class Materias extends Controllers{
        
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login']))
			{
				header('Location: '.base_url().'/login');
			}
			getPermisos(10);//Id del módulo en la base de datos Extrayendo los permisos del modulo logueado
		}

		public function index()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
			}
			$data['page_tag'] = "Materias";
			$data['page_title'] = "Materias <small></small>";
			$data['page_name'] = "Materias";
			$data['page_functions_js'] = "functions_materias.js";//Por cambia atento---------------------
			$this->views->getView($this,"materias",$data);
		}

        public function insertNewMateria(){
            if ($_POST) {
                //si viene VACIO alguno de estos campos
                if (empty($_POST['txtArea']) || empty($_POST['txtCampo']) || empty($_POST['listNivel']) || empty($_POST['listGrado']) || !isset($_POST['Status']) || empty($_POST['horas'])) {
                    $arrResponse=array("status"=>false,"msg"=>'No se recibieron los datos Correctamente');
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
                }
                    // Almacenamos los datos en variables 
                    $strArea=       ucwords(strClean($_POST['txtArea']));
                    $strNombre=     ucwords(strClean($_POST['txtCampo']));
                    $strDescripcion=strClean($_POST['txtDescripcion']);
                    $intGrado=      intval(strClean($_POST['listGrado']));
                    $strNivel=      strClean($_POST['listNivel']);
                    $intHoras=      intval(strClean($_POST['horas']));
                    $status=        intval(strClean($_POST['Status']));

                    $intIdMateria=        intval($_POST['idMateria'] ?? 0); //Sirve para validar si nos envían un ID para saber si es insert o update
                    // Ejecutamos el metodo del modelo 
                    $request_user="";
                    if ($intIdMateria > 0) {
                        if (!$_SESSION['permisosMod']['u']) {
                            $arrResponse=array("status"=>false,"msg"=>'Error. Usted no tiene permiso para ejecutar la acción.');
                            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
                        }
                        $request_user=$this->model->updateMateria($intIdMateria,$strArea, $strNombre, $strDescripcion, $intGrado, $strNivel, $intHoras, $status);
                    }else {
                        if (!$_SESSION['permisosMod']['w']) {
                            $arrResponse=array("status"=>false,"msg"=>'Error. Usted no tiene permiso para ejecutar la acción.');
                            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
                        }
                        $request_user=$this->model->insertMateria($strArea, $strNombre, $strDescripcion, $intGrado, $strNivel, $intHoras, $status);
                    }

                    //Validamos si se logro insertar el resultado 
                    if ($request_user == "dato_guardado") {
                        $msg = $intIdMateria > 0 ? 'Materia actualizada satisfactoriamente.' : 'Materia registrada satisfactoriamente.';
                        $arrResponse=array("status"=>true,"msg"=>$msg);

                    }else {
                        //Si la materia ya existe
                        if ($request_user=="exist") {
                            $arrResponse=array("status"=>false,"msg"=>'La materia ya existe en este grado.');
                        }else {
                            //Si ocurrió otro error
                            $arrResponse=array("status"=>false,"msg"=>'No es posible guardar datos: '.$request_user);

                        }
                    }

                //Decodifica la respuesta que viene del MODELO para mostrar en las vistas
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                die();
            }       
        }
// --------------------------------------------------------------------------------------------------------------------------------------------



        public function getMaterias()
        {
            if($_SESSION['permisosMod']['r']){
                $arrData = $this->model->selectMaterias();
                for ($i=0; $i < count($arrData); $i++) {
                    $btnView = '';
                    $btnEdit = '';
                    $btnDelete = '';

                    if($arrData[$i]['status'] == 1)
                    {
                        $arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
                    }else{
                        $arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
                    }

                    if($_SESSION['permisosMod']['r']){
                        $btnView = '<button class="btn btn-info btn-sm btnViewMateria" onClick="fntViewMateria('.$arrData[$i]['id_materia'].')" title="Ver materia"><i class="far fa-eye"></i></button>';
                    }
                    if($_SESSION['permisosMod']['u']){
                        $btnEdit = '<button class="btn btn-primary  btn-sm btnEditMateria" onClick="fntEditMateria(this,'.$arrData[$i]['id_materia'].')" title="Editar materia"><i class="fas fa-pencil-alt"></i></button>';
                    }
                    if($_SESSION['permisosMod']['d']){
                        $btnDelete = '<button class="btn btn-danger btn-sm btnDelMateria" onClick="fntDelMateria('.$arrData[$i]['id_materia'].')" title="Eliminar materia"><i class="far fa-trash-alt"></i></button>';

                    }
                    $arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
                }
                echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        // --------------------------------------------------------------------------------------------------------------------------------------------
        public function getMateria($idMateria){
            if($_SESSION['permisosMod']['r']){
                $IdMat = intval($idMateria);
                if($IdMat > 0) //Validamos que tengamos un ID válido
                {
                    $arrData = $this->model->selectMateria($IdMat);
                    if(empty($arrData))
                    {
                        $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
                    }else{
                        $arrResponse = array('status' => true, 'data' => $arrData);
                    }
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                }
            }
            die();
        }
        // --------------------------------------------------------------------------------------------------------------------------------------------
        public function delMateria()
        {
            if($_POST){
                if($_SESSION['permisosMod']['d']){
            
                    $intIdMateria = intval($_POST['id_Materia']);
                    $requestDelete = $this->model->deleteMateria($intIdMateria);
                    if($requestDelete)
                    {
                        $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado la Materia');
                    }else{
                        $arrResponse = array('status' => false, 'msg' => 'Error al eliminar Materia.');
                    }
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                }
            }
            die();
        }

    }

?>