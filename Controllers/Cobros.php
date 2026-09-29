<?php
    class Cobros extends Controllers{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login']))
			{
				header('Location: '.base_url().'/login');
			}
			getPermisos(6);//Id del módulo en la base de datos Extrayendo los permisos del modulo logueado
		}

		public function index()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
			}
			$data['page_tag'] = "Cobros";
			$data['page_title'] = "Cobros <small>Cuotas extra</small>";
			$data['page_name'] = "Cobros";
			$data['page_functions_js'] = "functions_cobros.js";
			$this->views->getView($this,"cobros",$data);
		}

		public function getCobros()
		{
			if ($_SESSION['permisosMod']['r']) {
				$arrData = $this->model->selectCobros();
				for ($i=0; $i < count($arrData); $i++) {
					$btnView = ''; $btnEdit = ''; $btnDelete = '';
					$arrData[$i]['status'] = \Services\Presenter::estado(intval($arrData[$i]['status'] ?? 0));
					$arrData[$i]['valor'] = \Services\FinanzasService::bolivianos($arrData[$i]['valor'] ?? 0);
					if($_SESSION['permisosMod']['r']){
						$btnView = '<button class="btn btn-info btn-sm" onClick="fntViewCobro('.$arrData[$i]['id_cobros'].')" title="Ver"><i class="far fa-eye"></i></button>';
					}
					if($_SESSION['permisosMod']['u']){
						$btnEdit = '<button class="btn btn-primary btn-sm" onClick="fntEditCobro('.$arrData[$i]['id_cobros'].')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
					}
					if($_SESSION['permisosMod']['d']){
						$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelCobro('.$arrData[$i]['id_cobros'].')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';
					}
					$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
				}
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function getCobro(int $id)
		{
			if ($_SESSION['permisosMod']['r'] && $id > 0) {
				$arrData = $this->model->selectCobro($id);
				if (empty($arrData)) {
					echo json_encode(array('status'=>false,'msg'=>'Datos no encontrados.'),JSON_UNESCAPED_UNICODE);
				}else{
					echo json_encode(array('status'=>true,'data'=>$arrData),JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}

		public function saveCobro()
		{
		if (!$_POST) { die(); }
		$id = intval($_POST['idCobro'] ?? 0);
		// REV-SVC: normalización y validación en el servicio
		$norm = \Services\CobroService::normalizar([
			'nombre' => ucwords(strClean($_POST['txtTituloCobro'] ?? '')),
			'descripcion' => strClean($_POST['txtDesPago'] ?? ''),
			'tipo' => strClean($_POST['listTipoPago'] ?? 'Unico'),
			'ncuota' => intval($_POST['intCuota'] ?? 1),
			'valor' => floatval($_POST['txtMonto'] ?? 0),
			'status' => intval($_POST['status'] ?? 1),
		]);
		if (!$norm->ok) {
			echo json_encode(array('status'=>false,'msg'=>$norm->msg),JSON_UNESCAPED_UNICODE);die();
		}
		if ($id > 0) {
			if (!$_SESSION['permisosMod']['u']) { echo json_encode(array('status'=>false,'msg'=>'Sin permiso.'),JSON_UNESCAPED_UNICODE);die(); }
		}else{
			if (!$_SESSION['permisosMod']['w']) { echo json_encode(array('status'=>false,'msg'=>'Sin permiso.'),JSON_UNESCAPED_UNICODE);die(); }
		}
		$svc = new \Services\CobroService($this->model);
		$r = $svc->guardar($id, $norm->data);
		echo json_encode(array('status'=>$r->ok,'msg'=>$r->msg),JSON_UNESCAPED_UNICODE);
		die();
		}

		public function delCobro()
		{
			if ($_POST && $_SESSION['permisosMod']['d']) {
				$id = intval($_POST['idCobro'] ?? 0);
				if ($id > 0 && $this->model->deleteCobro($id)) {
					echo json_encode(array('status'=>true,'msg'=>'Cobro eliminado.'),JSON_UNESCAPED_UNICODE);
				}else{
					echo json_encode(array('status'=>false,'msg'=>'Error al eliminar.'),JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}
    }

?>
