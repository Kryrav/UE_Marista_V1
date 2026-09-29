<?php
    class Tutores extends Controllers{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login']))
			{
				header('Location: '.base_url().'/login');
			}
			getPermisos(12);
		}

		public function index()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
			}
			$data['page_tag'] = "Tutores";
			$data['page_title'] = "Tutores <small>Padres y apoderados</small>";
			$data['page_name'] = "Tutores";
			$data['page_functions_js'] = "functions_tutores.js";
			$this->views->getView($this,"tutores",$data);
		}

		public function getTutores()
		{
			if ($_SESSION['permisosMod']['r']) {
				$arrData = $this->model->selectTutores();
				for ($i=0; $i < count($arrData); $i++) {
					$btnView=''; $btnEdit=''; $btnDelete='';
					$arrData[$i]['status_padre'] = \Services\Presenter::estado(intval($arrData[$i]['status_padre'] ?? 0));
					if($_SESSION['permisosMod']['r']){
						$btnView = '<button class="btn btn-info btn-sm" onClick="fntViewTutor('.$arrData[$i]['id_padre'].')" title="Ver"><i class="far fa-eye"></i></button>';
					}
					if($_SESSION['permisosMod']['u']){
						$btnEdit = '<button class="btn btn-primary btn-sm" onClick="fntEditTutor('.$arrData[$i]['id_padre'].')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
					}
					if($_SESSION['permisosMod']['d']){
						$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelTutor('.$arrData[$i]['id_padre'].')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';
					}
					$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
				}
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function getTutor(int $id)
		{
			if ($_SESSION['permisosMod']['r'] && $id > 0) {
				$arrData = $this->model->selectTutor($id);
				if(empty($arrData)){ echo json_encode(array('status'=>false,'msg'=>'Datos no encontrados.'),JSON_UNESCAPED_UNICODE); }
				else{ echo json_encode(array('status'=>true,'data'=>$arrData),JSON_UNESCAPED_UNICODE); }
			}
			die();
		}

		public function getByEstudiante(int $idEstudiante)
		{
			if ($_SESSION['permisosMod']['r'] && $idEstudiante > 0) {
				echo json_encode(array('status'=>true,'data'=>$this->model->selectByEstudiante($idEstudiante)),JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function listEstudiantes()
		{
			if ($_SESSION['permisosMod']['r']) {
				$arr = $this->model->listEstudiantesOptions();
				$html = '<option value="">Seleccione estudiante...</option>';
				foreach($arr as $e){ $html .= '<option value="'.$e['id_estudiante'].'">'.htmlspecialchars($e['label']).'</option>'; }
				echo $html;
			}
			die();
		}

		// Autocomplete: ?q=CI/RUDE/nombre (top 8 con curso y n° de tutores)
		public function buscarEstudiante()
		{
			if($_SESSION['permisosMod']['r']){
				$q = trim(strClean($_GET['q'] ?? ''));
				if(strlen($q) < 2){ echo json_encode(array('status'=>true,'data'=>[]), JSON_UNESCAPED_UNICODE); die(); }
				echo json_encode(array('status'=>true,'data'=>$this->model->buscarEstudiantes($q) ?: []), JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		// ¿El CI ya es tutor? Si sí, se prellena (solo se vinculará, no se duplica)
		public function existeTutor(string $ci = '')
		{
			if($_SESSION['permisosMod']['r']){
				$ci = strClean($ci);
				$per = $this->model->findPersonaByCi($ci);
				if(empty($per)){ echo json_encode(array('status'=>false), JSON_UNESCAPED_UNICODE); die(); }
				unset($per['password']);
				echo json_encode(array('status'=>true,'data'=>$per,
					'esTutor'=> (intval($per['id_rol']) === 14)), JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function saveTutor()
		{
			if (!$_POST) { die(); }
			if(!csrf_check($_POST['csrf_token'] ?? null)){ echo json_encode(array('status'=>false,'msg'=>'Sesión expirada. Recargue la página.'),JSON_UNESCAPED_UNICODE);die(); }
			$idPadre = intval($_POST['idPadre'] ?? 0);
			$idEstudiante = intval($_POST['listEstudiante'] ?? 0);
			if($idEstudiante <= 0){ echo json_encode(array('status'=>false,'msg'=>'Seleccione el estudiante.'),JSON_UNESCAPED_UNICODE);die(); }
			foreach(['txtCiTutor','txtNombreTutor','txtApellidoTutor','txtCelTutor','txtEmailTutor'] as $c){
				if(empty($_POST[$c])){ echo json_encode(array('status'=>false,'msg'=>'Complete los datos del tutor.'),JSON_UNESCAPED_UNICODE);die(); }
			}
			// REV-SVC: armado y domicilio en el servicio (misma forma de datos)
			$svc = new \Services\TutorService($this->model);
			$dirTutor = $svc->resolverDomicilio(
				!empty($_POST['chkMismoDom']),
				strClean($_POST['txtDireccionTutor'] ?? ''),
				$idEstudiante
			);
			$d = \Services\TutorService::buildData([
				"ci" => strClean($_POST['txtCiTutor']),
				"nombre" => ucwords(strClean($_POST['txtNombreTutor'])),
				"apellido" => ucwords(strClean($_POST['txtApellidoTutor'])),
				"sexo" => strClean($_POST['listSexoTutor'] ?? 'M'),
				"cel" => strClean($_POST['txtCelTutor']),
				"email" => strtolower(strClean($_POST['txtEmailTutor'])),
				"status" => intval($_POST['listStatusTutor'] ?? 1),
				"password_plain" => trim($_POST['txtPasswordTutor'] ?? ''),
				"idPadre" => $idPadre,
				"parentesco" => strClean($_POST['listParentesco'] ?? 'Padre'),
				"nacionalidad" => strClean($_POST['txtNacionalidad'] ?? 'Boliviana'),
				"estado_civil" => strClean($_POST['listEstadoCivil'] ?? ''),
				"profesion" => strClean($_POST['txtProfesion'] ?? ''),
				"empresa" => strClean($_POST['txtEmpresa'] ?? ''),
				"observaciones" => strClean($_POST['txtObservaciones'] ?? ''),
			], $dirTutor);
			if($idPadre > 0){
				if(!$_SESSION['permisosMod']['u']){ echo json_encode(array('status'=>false,'msg'=>'Sin permiso.'),JSON_UNESCAPED_UNICODE);die(); }
			}else{
				if(!$_SESSION['permisosMod']['w']){ echo json_encode(array('status'=>false,'msg'=>'Sin permiso.'),JSON_UNESCAPED_UNICODE);die(); }
			}
			$r = $svc->guardar($idPadre, $d["tutor"], $idEstudiante, $d["padre"]);
			echo json_encode($r->toArray(), JSON_UNESCAPED_UNICODE);
			die();
		}

		public function delTutor()
		{
			if ($_POST && $_SESSION['permisosMod']['d']) {
				if(!csrf_check($_POST['csrf_token'] ?? null)){ echo json_encode(array('status'=>false,'msg'=>'Sesión expirada. Recargue la página.'),JSON_UNESCAPED_UNICODE); die(); }
				$id = intval($_POST['idPadre'] ?? 0);
				if($id > 0 && $this->model->deleteTutor($id)){ echo json_encode(array('status'=>true,'msg'=>'Tutor dado de baja.'),JSON_UNESCAPED_UNICODE); }
				else{ echo json_encode(array('status'=>false,'msg'=>'Error al eliminar.'),JSON_UNESCAPED_UNICODE); }
			}
			die();
		}
    }
?>
