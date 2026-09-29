<?php 

	class Gestion extends Controllers{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login']))
			{
				header('Location: '.base_url().'/login');
			}
			getPermisos(11);
		}

		public function index()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
			}
			$data['page_tag'] = "Gestión";
			$data['page_title'] = "Gestión <small></small>";
			$data['page_name'] = "Gestión";
			$data['page_functions_js'] = "functions_gestion.js";
			$this->views->getView($this,"gestion",$data);
		}

		public function	getGestionesPas()
		{

			$arrData=$this->model->getGestiones();
			for ($i=0; $i < count($arrData); $i++) { 
				if($arrData[$i]['status'] == 1)
				{
					$arrData[$i]['status']= '<span class="badge badge-success">Abierto</span>';
				}else {
					$arrData[$i]['status']= '<span class="badge badge-danger">Cerrado</span>';

				}
			}
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			die();
		}

		public function getGestionAct()
		{
			// Consultamos si tiene permiso de ver esta sección de Gestion 
			if ($_SESSION['permisosMod']['r']) {
				$arrData=$this->model->selectGestionAct();
				if (empty($arrData)) {
					$arrResponse	=	array	('status'=>false,'msg'=>'No hay gestión activa.');
				}else {
					$arrResponse	=	array	('status'=>true,'data'=>$arrData);
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			
			die();
			
		}

		public function insertGestion()
		{
			
			if(empty($_POST['anio']) || empty($_POST['fechaInicio']) || empty($_POST['fechaFin']) || empty($_POST['intPension']) || empty($_POST['txtDescripcion']) )
			{
				$arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
			}else {
				$intGestion=intval(strClean($_POST['anio']));
				$dateInicio=(strClean($_POST['fechaInicio']));
				$dateFin=(strClean($_POST['fechaFin']));
				$strGest=(strClean($_POST['anio']));
				$intPension=intval(strClean($_POST['intPension']));
				$strDescripcion=(strClean($_POST['txtDescripcion']));
				$boolNew=(strClean($_POST['newG']));
			
			$arrData="";
			// Validamos si es insertar nueva gestión o  es actualizar gestión 
			// REV-SVC: apertura/actualización en el servicio (misma semántica)
			// El servicio devuelve el código crudo para conservar el mapeo de mensajes.
			$svc = new \Services\GestionService($this->model);
			if ($boolNew) {
				// Si es una apertura de gestion
				if ($_SESSION['permisosMod']['w']) {
					$r = $svc->guardar(true, $intGestion, $dateInicio, $dateFin, $strGest, $intPension, $strDescripcion);
					$arrData = $r->ok ? "dato_guardado" : $r->msg;
				}
			} else {
				// Es la actualización de la gestión 
				if ($_SESSION['permisosMod']['u']) {
					$r = $svc->guardar(false, $intGestion, $dateInicio, $dateFin, $strGest, $intPension, $strDescripcion);
					$arrData = $r->ok ? "dato_guardado" : $r->msg;
				}
			}
				
				if($arrData == "dato_guardado" )
				{
					$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
				
				}else if($arrData == 'Error en la consulta!.'){
					$arrResponse = array('status' => false, 'msg' => '¡Atención! No se pudo realizar la operación.');		
				}else{
					$arrResponse = array("status" => false, "msg" => 'ERROR: '.$arrData);
				}
				

			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			die();
			
		}

		public function closeGestion()
		{
			if ($_SESSION['permisosMod']['d']) {
				$requestDelete = $this->model->deleteGestion();
					if($requestDelete)
					{
						$arrResponse = array('status' => true, 'msg' => 'Se ha cerrado la gestión');
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Error al cerrar la gestión.');
					}
					
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			die();
		}
    }
?>