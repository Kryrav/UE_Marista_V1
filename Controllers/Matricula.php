<?php
    class Matricula extends Controllers{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login']))
			{
				header('Location: '.base_url().'/login');
			}
			getPermisos(4);//Id del módulo en la base de datos Extrayendo los permisos del modulo logueado
		}

		public function index()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
			}
			$data['page_tag'] = "Matrícula";
			$data['page_title'] = "Matrícula <small>Registro</small>";
			$data['page_name'] = "Matrícula";
			$data['page_functions_js'] = "functions_matricula.js";
			$this->views->getView($this,"matricula",$data);
		}

		// Stub legacy eliminado: usar insertNewMatricula / getMatricula / delMatricula

		// LISTA TODAS LAS MATRÍCULAS REGISTRADAS EN TODAS LAS GESTIONES 
		public function getMatriculaAll()
		{
			if ($_SESSION['permisosMod']['r']) {
				$arrData=$this->model->selectMatriculasAll();
				for ($i=0; $i < count($arrData); $i++) {
                    $btnView = '';
                    $btnEdit = '';
                    $btnDelete = '';

                    if($arrData[$i]['estado_matricula'] == 1)
                    {
                        $arrData[$i]['estado_matricula'] = '<span class="badge badge-success">Activo</span>';
                    }else{
                        $arrData[$i]['estado_matricula'] = '<span class="badge badge-danger">Inactivo</span>';
                    }
                    // ITERACIÓN 1: feedback visual de documentación pendiente (U-03/U-04)
                    $ei = trim($arrData[$i]['estado_inscripcion'] ?? '');
                    if($ei === 'Pendiente_Documentos'){
                        $plazo = trim($arrData[$i]['plazo_documentos_hasta'] ?? '');
                        $alerta = '';
                        if($plazo !== '' && function_exists('diasHabilesRestantes')){
                            try { $r = diasHabilesRestantes($plazo); $alerta = $r < 0 ? ' (vencido '.abs($r).'d)' : " ($r d)"; } catch(Exception $x){}
                        }
                        $arrData[$i]['estado_inscripcion'] = '<span class="badge badge-warning" title="Plazo: '.htmlspecialchars($plazo).'">Pendiente docs'.$alerta.'</span>';
                    }elseif($ei === 'Confirmado'){
                        $arrData[$i]['estado_inscripcion'] = '<span class="badge badge-success">Confirmado</span>';
                    }elseif($ei === 'Inscrito'){
                        $arrData[$i]['estado_inscripcion'] = '<span class="badge badge-info">Inscrito</span>';
                    }

                    if($_SESSION['permisosMod']['r']){
                        $btnView = '<button class="btn btn-info btn-sm btnViewMatricula" onClick="fntViewMatricula('.$arrData[$i]['id_matricula'].')" title="Ver Matrícula"><i class="far fa-eye"></i></button>';
                    }
                    if($_SESSION['permisosMod']['u']){
                        $btnEdit = '<button class="btn btn-primary  btn-sm btnEditMatricula" onClick="fntEditMatricula(this,'.$arrData[$i]['id_matricula'].')" title="Editar Matrícula"><i class="fas fa-pencil-alt"></i></button>';
                    }
                    if($_SESSION['permisosMod']['d']){
                        $btnDelete = '<button class="btn btn-danger btn-sm btnDelMatricula" onClick="fntDelMatricula('.$arrData[$i]['id_matricula'].')" title="Eliminar Matrícula"><i class="far fa-trash-alt"></i></button>';

                    }
                    $arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
                }
                echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		// INSERTAR / ACTUALIZAR MATRÍCULA 
		// ITERACIÓN 1: estado Pendiente_Documentos + plazo 30 días hábiles + checklist.
		public function insertNewMatricula()
		{
			if ($_POST) {
				if (empty($_POST['intGestion'])||empty($_POST['listParalelos'])||empty($_POST['listTipoEstudiante'])||empty($_POST['listStateInscripcion'])||!isset($_POST['newG'])) {
					$arrResponse=array("status"=>false,"msg"=>'No se recibieron los datos Correctamente');
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
				}
				// Almacenamos los datos en variables (folio es texto libre, no intval)
				$boolNew = strClean($_POST['newG']) == "1";
				$strCi = strClean($_POST['txtCi'] ?? '');
				$intGestion=intval($_POST['intGestion']);
				$intIdParalelo=intval($_POST['listParalelos']);
				$strTipoMatricula=strClean($_POST['listTipoEstudiante']);
				$strFolio=strClean($_POST['txtFolio'] ?? '');
				$strStatus=strClean($_POST['listStateInscripcion']);
				$intIdMatricula=intval($_POST['idMatricula'] ?? 0);
				// Documentación diferida (opcionales, no bloquean)
				$docPend = !empty($_POST['chkDocPendiente']) || $strStatus === 'Pendiente_Documentos';
				if($docPend){ $strStatus = 'Pendiente_Documentos'; }
				$plazo = trim($_POST['plazoDocs'] ?? '');
				if($docPend && $plazo === ''){
					$plazo = function_exists('plazo30Habiles') ? plazo30Habiles() : date('Y-m-d', strtotime('+30 days'));
				} elseif($plazo === ''){ $plazo = null; }
				$chkDocs = [
					'ci' => 1,
					'cert_nac' => !empty($_POST['doc_cert_nac']) ? 1 : 0,
					'rude' => !empty($_POST['doc_rude']) ? 1 : 0,
					'solicitud' => !empty($_POST['doc_solicitud']) ? 1 : 0,
				];
				$comp = !empty($_POST['chkCompromiso']) ? 1 : ($docPend ? 1 : 0);
				$obsDocs = strClean($_POST['docsObs'] ?? '');

				$request_user="";

				//Preguntamos si es nuevo registro o actualización 
				if ($boolNew) {
					if (empty($strCi)) {
						$arrResponse=array("status"=>false,"msg"=>'El CI del estudiante es obligatorio.');
						echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
					}
					if ($_SESSION['permisosMod']['w']) {
						$request_user=$this->model->insertMatricula($strCi,$intGestion,$intIdParalelo,$strTipoMatricula,$strFolio,$strStatus);
						if($request_user == "matricula_guardada" && ($docPend || $plazo !== null)){
							$this->model->setDocumentacionByCiGestion($strCi,$intGestion,$plazo,$chkDocs,$comp,$obsDocs,$strStatus);
						}
					}else {
						$arrResponse=array("status"=>false,"msg"=>'Error. Usted no tiene permiso para ejecutar la acción.');
						echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
					}
				}else {
					if ($intIdMatricula <= 0) {
						$arrResponse=array("status"=>false,"msg"=>'ID de matrícula inválido para actualizar.');
						echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
					}
					if ($_SESSION['permisosMod']['u']) {
						$request_user=$this->model->updateMatricula($intIdMatricula,$intIdParalelo,$strTipoMatricula,$strFolio,$strStatus);
						if($request_user == "matricula_actualizada"){
							// Si pasa a Confirmado y no es pendiente, se libera el plazo
							if($strStatus === 'Pendiente_Documentos'){
								$this->model->setDocumentacion($intIdMatricula,$plazo,$chkDocs,$comp,$obsDocs,$strStatus);
							}else{
								// Mantiene checklist pero libera plazo si ya completó
								$this->model->setDocumentacion($intIdMatricula, null, $chkDocs, $comp, $obsDocs, $strStatus);
							}
						}
					}else {
						$arrResponse=array("status"=>false,"msg"=>'Error. Usted no tiene permiso para ejecutar la acción.');
						echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
					}						
				}

				//Validamos si se logro insertar el resultado 
				if ($request_user == "matricula_guardada") {
					$msg='Matrícula registrada satisfactoriamente.';
					if($docPend && $plazo){ $msg .= " Documentación pendiente hasta $plazo (30 días hábiles)."; }
					$arrResponse=array("status"=>true,"msg"=>$msg);
				}else if ($request_user == "matricula_actualizada") {
					$arrResponse=array("status"=>true,"msg"=>'Matrícula actualizada satisfactoriamente.');
				}else {
					if ($request_user=="matricula_existente") {
						$arrResponse=array("status"=>false,"msg"=>'La matrícula ya existe (estudiante ya matriculado en esa gestión).');
					}else {
						$arrResponse=array("status"=>false,"msg"=>'No es posible guardar datos: '.$request_user);
					}
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				die();
			}
		}

		// OBTENER UNA MATRÍCULA
		public function getMatricula(int $idMatricula)
		{
			if ($_SESSION['permisosMod']['r']) {
				$id = intval($idMatricula);
				if ($id > 0) {
					$arrData = $this->model->selectMatricula($id);
					if (empty($arrData)) {
						$arrResponse = array('status'=>false,'msg'=>'Datos no encontrados.');
					}else{
						$arrResponse = array('status'=>true,'data'=>$arrData);
					}
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}

		// BAJA LÓGICA DE MATRÍCULA
		public function delMatricula()
		{
			if ($_POST && $_SESSION['permisosMod']['d']) {
				$intId = intval($_POST['idMatricula'] ?? 0);
				if ($intId > 0 && $this->model->deleteMatricula($intId)) {
					$arrResponse = array('status'=>true,'msg'=>'Matrícula dada de baja.');
				}else{
					$arrResponse = array('status'=>false,'msg'=>'Error al eliminar la matrícula.');
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}
	}	

?>