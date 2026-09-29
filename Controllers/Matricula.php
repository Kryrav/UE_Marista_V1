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
			// I2-addenda: gestión activa visible para la regla de matriculación
			require_once("Models/GestionModel.php");
			$gm = new GestionModel();
			$act = $gm->selectGestionAct();
			$data['gestion_activa'] = intval(is_array($act) ? ($act["gestion"] ?? 0) : 0);
			$this->views->getView($this,"matricula",$data);
		}

		// I2-addenda: la matriculación opera sobre la gestión activa.
		// Solo Admin (1) / Director (2) pueden rectificar otra gestión con motivo.
		private function gestionActivaAnio(): int
		{
			require_once("Models/GestionModel.php");
			$gm = new GestionModel();
			$act = $gm->selectGestionAct();
			return intval(is_array($act) ? ($act["gestion"] ?? 0) : 0);
		}

		private function puedeRectificarGestion(): bool
		{
			$rol = intval($_SESSION['userData']['idrol'] ?? 0);
			return ($rol === 1 || $rol === 2);
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
                    // ITERACIÓN 2: estados terminales con historial preservado (F-09)
                    }elseif($ei === 'Retirado'){
                        $mot = trim($arrData[$i]['motivo_estado'] ?? '');
                        $arrData[$i]['estado_inscripcion'] = '<span class="badge badge-secondary"'.($mot !== '' ? ' title="'.htmlspecialchars($mot).'"' : '').'>Retirado</span>';
                    }elseif($ei === 'Trasladado'){
                        $mot = trim($arrData[$i]['motivo_estado'] ?? '');
                        $arrData[$i]['estado_inscripcion'] = '<span class="badge badge-dark"'.($mot !== '' ? ' title="'.htmlspecialchars($mot).'"' : '').'>Trasladado</span>';
                    }elseif($ei === 'Egresado'){
                        $arrData[$i]['estado_inscripcion'] = '<span class="badge badge-primary"'.(trim($arrData[$i]['motivo_estado'] ?? '') !== '' ? ' title="'.htmlspecialchars($arrData[$i]['motivo_estado']).'"' : '').'>Egresado</span>';
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
                    // ITERACIÓN 2: rematricular directo desde la fila (usa CI de la fila)
                    $btnRem = '';
                    if($_SESSION['permisosMod']['w']){
                        $btnRem = '<button class="btn btn-success btn-sm" onClick="fntRematricular(\''.htmlspecialchars($arrData[$i]['ci_estudiante'] ?? '', ENT_QUOTES).'\')" title="Rematricular en siguiente gestión"><i class="fas fa-forward"></i></button>';
                    }
                    // I3 (U-05): comprobante imprimible
                    $btnComp = '';
                    if($_SESSION['permisosMod']['r']){
                        $btnComp = '<button class="btn btn-secondary btn-sm" onClick="fntComprobante('.$arrData[$i]['id_matricula'].')" title="Comprobante de matrícula"><i class="fa fa-print"></i></button>';
                    }
                    $arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.' '.$btnRem.' '.$btnComp.'</div>';
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
				if(!csrf_check($_POST['csrf_token'] ?? null)){ $arrResponse=array("status"=>false,"msg"=>'Sesión expirada. Recargue la página.'); echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die(); }
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
				// ITERACIÓN 2: motivo obligatorio en estados terminales (auditoría mínima F-09)
				$motivoEstado=strClean($_POST['motivoEstado'] ?? '');
				if(function_exists('esEstadoTerminal') && esEstadoTerminal($strStatus) && $motivoEstado === ''){
					$arrResponse=array("status"=>false,"msg"=>'Indique el motivo del cambio a '.$strStatus.'.');
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
				}
				// I2-addenda: solo gestión activa, salvo rectificación Admin/Director con motivo
				$motivoRect=strClean($_POST['motivoRectificacion'] ?? '');
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
					// I2-addenda: gestión activa obligatoria salvo rectificación autorizada
					$gAct = $this->gestionActivaAnio();
					$esRect = ($gAct > 0 && $intGestion !== $gAct);
					if($esRect){
						if(!$this->puedeRectificarGestion()){
							$arrResponse=array("status"=>false,"msg"=>'Solo se matricula en la gestión activa ('.$gAct.'). Solicite rectificación a Dirección.');
							echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
						}
						if($motivoRect === ''){
							$arrResponse=array("status"=>false,"msg"=>'Rectificación fuera de gestión activa: indique el motivo.');
							echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
						}
					}
					if ($_SESSION['permisosMod']['w']) {
						$uid = intval($_SESSION['idUser'] ?? 0) ?: null; // I3 auditoría
						$request_user=$this->model->insertMatricula($strCi,$intGestion,$intIdParalelo,$strTipoMatricula,$strFolio,$strStatus,$uid);
						if($request_user == "matricula_guardada" && ($docPend || $plazo !== null)){
							$this->model->setDocumentacionByCiGestion($strCi,$intGestion,$plazo,$chkDocs,$comp,$obsDocs,$strStatus);
						}
						if($request_user == "matricula_guardada" && $esRect){
							$this->model->setMotivoByCiGestion($strCi,$intGestion,'Rectificación histórica: '.$motivoRect);
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
						$uid = intval($_SESSION['idUser'] ?? 0) ?: null; // I3 auditoría
						$request_user=$this->model->updateMatricula($intIdMatricula,$intIdParalelo,$strTipoMatricula,$strFolio,$strStatus,$motivoEstado,$uid,$motivoRect);
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
					if(!empty($esRect)){ $msg .= ' (Rectificación fuera de gestión activa, con motivo registrado).'; }
					$last = $this->model->ultimaMatriculaByCi($strCi);
					$arrResponse=array("status"=>true,"msg"=>$msg,
						"idMatricula"=>intval($last['id_matricula'] ?? 0),
						"idEstudiante"=>intval($last['id_estudiante'] ?? 0));
				}else if ($request_user == "matricula_actualizada") {
					$arrResponse=array("status"=>true,"msg"=>'Matrícula actualizada satisfactoriamente.',
						"idMatricula"=>$intIdMatricula, "idEstudiante"=>0);
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

		// FLUJO-ÓPTIMO (1,2): resumen de confirmación SIN escribir. Tolera CI nuevo
		// (alta lo creará). No exige CSRF por ser solo lectura.
		public function preview()
		{
			if(empty($_SESSION['permisosMod']['r'])){ echo json_encode(array('status'=>false,'msg'=>'Sin permiso.'),JSON_UNESCAPED_UNICODE); die(); }
			$ci = strClean($_GET['ci'] ?? ($_POST['ci'] ?? ''));
			$gestion = intval($_GET['gestion'] ?? ($_POST['gestion'] ?? 0));
			$paralelo = intval($_GET['paralelo'] ?? ($_POST['paralelo'] ?? 0));
			$tipo = strClean($_GET['tipo'] ?? ($_POST['tipo'] ?? 'Regular'));
			$estado = strClean($_GET['estado'] ?? ($_POST['estado'] ?? 'Inscrito'));
			if($ci === '' || $gestion < 2000 || $paralelo <= 0){
				echo json_encode(array('status'=>false,'msg'=>'Indique CI, gestión y paralelo.'),JSON_UNESCAPED_UNICODE); die();
			}
			$est = $this->model->select(
				"SELECT e.id_estudiante, p.ci, p.nombre, p.apellido FROM estudiante e
				 INNER JOIN persona p ON e.id_persona = p.id_persona WHERE p.ci = ?", [$ci]);
			$par = $this->model->select(
				"SELECT p.id_paralelo, p.nivel, p.grado, p.sigla, p.turno, p.cupo,
					(SELECT COUNT(*) FROM matricula m WHERE m.id_paralelo = p.id_paralelo AND m.gestion = ? AND m.status = 1) AS inscritos
				 FROM paralelo p WHERE p.id_paralelo = ?", [$gestion, $paralelo]);
			$gm = $this->model->select("SELECT monto_pension FROM gestion WHERE gestion = ?", [$gestion]);
			$dup = null;
			$tutores = 0;
			if(!empty($est)){
				$dup = $this->model->select(
					"SELECT id_matricula, estado_inscripcion FROM matricula WHERE id_estudiante = ? AND gestion = ? ORDER BY id_matricula DESC LIMIT 1",
				 [$est['id_estudiante'], $gestion]);
				$t = $this->model->select(
					"SELECT COUNT(*) AS c FROM padre WHERE id_estudiante = ? AND status != 0", [$est['id_estudiante']]);
				$tutores = intval($t['c'] ?? 0);
			}
			$monto = floatval($gm['monto_pension'] ?? 0);
			$warn = [];
			if(empty($est)){ $warn[] = 'Estudiante nuevo: se creará con el alta.'; }
			if($dup){ $warn[] = 'Ya matriculado en '.$gestion.' ('.$dup['estado_inscripcion'].'): se bloqueará el duplicado.'; }
			if($par){
				$libres = intval($par['cupo']) - intval($par['inscritos']);
				if($libres <= 0){ $warn[] = 'Paralelo SIN CUPO.'; }
			} else { $warn[] = 'Paralelo inexistente.'; }
			if($monto <= 0){ $warn[] = 'Gestión sin monto de pensión configurado.'; }
			if(!empty($est) && $tutores === 0){ $warn[] = 'Sin tutores vinculados (se puede vincular después).'; }
			echo json_encode(array('status'=>true,'data'=>array(
				'estudiante' => $est ?: null,
				'paralelo' => $par ?: null,
				'gestion' => $gestion, 'tipo' => $tipo, 'estado' => $estado,
				'monto' => $monto, 'cuotas' => 10, 'total' => round($monto * 10, 2),
				'tutores' => $tutores, 'duplicado' => !empty($dup),
				'warnings' => $warn,
			)), JSON_UNESCAPED_UNICODE);
			die();
		}

		// ITERACIÓN 2 (F-04): última matrícula por CI para precargar la rematriculación.
		public function ultimaMatricula()
		{
			if ($_SESSION['permisosMod']['r']) {
				$ci = strClean($_GET['ci'] ?? ($_POST['ci'] ?? ''));
				if($ci === ''){ echo json_encode(array('status'=>false,'msg'=>'Indique el CI.'),JSON_UNESCAPED_UNICODE); die(); }
				$last = $this->model->ultimaMatriculaByCi($ci);
				if(empty($last)){ echo json_encode(array('status'=>false,'msg'=>'Sin matrículas previas para ese CI.'),JSON_UNESCAPED_UNICODE); die(); }
				require_once("Models/GestionModel.php");
				$gm = new GestionModel();
				$act = $gm->selectGestionAct();
				echo json_encode(array('status'=>true,'data'=>$last,'gestion_activa'=>intval($act["gestion"] ?? date("Y"))),JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		// ITERACIÓN 2 (F-04): rematricular regular en la gestión destino.
		// Reutiliza insertMatricula (SP genera las 10 pensiones); no duplica por gestión.
		public function rematricular()
		{
			if ($_POST) {
				if(!csrf_check($_POST['csrf_token'] ?? null)){ echo json_encode(array("status"=>false,"msg"=>'Sesión expirada. Recargue la página.'),JSON_UNESCAPED_UNICODE);die(); }
				if(!$_SESSION['permisosMod']['w']){
					echo json_encode(array("status"=>false,"msg"=>'Sin permiso para rematricular.'),JSON_UNESCAPED_UNICODE);die();
				}
				$ci = strClean($_POST['ci'] ?? '');
				$gestion = intval($_POST['gestion'] ?? 0);
				$paralelo = intval($_POST['paralelo'] ?? 0);
				$tipo = strClean($_POST['tipo'] ?? 'Regular');
				if($ci === '' || $gestion < 2000 || $gestion > 2100 || $paralelo <= 0){
					echo json_encode(array("status"=>false,"msg"=>'Datos incompletos: CI, gestión válida y paralelo.'),JSON_UNESCAPED_UNICODE);die();
				}
				// I2-addenda: destino = gestión activa, salvo rectificación autorizada
				$gAct = $this->gestionActivaAnio();
				if($gAct > 0 && $gestion !== $gAct){
					$motivoRect = strClean($_POST['motivoRectificacion'] ?? '');
					if(!$this->puedeRectificarGestion()){
						echo json_encode(array("status"=>false,"msg"=>'Solo se rematricula en la gestión activa ('.$gAct.').'),JSON_UNESCAPED_UNICODE);die();
					}
					if($motivoRect === ''){
						echo json_encode(array("status"=>false,"msg"=>'Rectificación fuera de gestión activa: indique el motivo.'),JSON_UNESCAPED_UNICODE);die();
					}
				}
				if($tipo === ''){ $tipo = 'Regular'; }
				$last = $this->model->ultimaMatriculaByCi($ci);
				if(empty($last)){
					echo json_encode(array("status"=>false,"msg"=>'El CI no tiene matrículas previas; use Nueva Matrícula.'),JSON_UNESCAPED_UNICODE);die();
				}
				$res = $this->model->insertMatricula($ci, $gestion, $paralelo, $tipo, '', 'Inscrito', intval($_SESSION['idUser'] ?? 0) ?: null);
				if($res == "matricula_guardada"){
					if($gAct > 0 && $gestion !== $gAct){
						$this->model->setMotivoByCiGestion($ci, $gestion, 'Rectificación histórica: '.strClean($_POST['motivoRectificacion'] ?? ''));
					}
					$cursoAnt = $last['curso'] ?? '—';
					$idEst = intval($last['id_estudiante'] ?? 0);
					$new = $this->model->ultimaMatriculaByCi($ci);
					echo json_encode(array("status"=>true,"msg"=>"Rematriculado en gestión $gestion (10 pensiones generadas). Curso anterior: $cursoAnt.",
						"idMatricula"=>intval($new['id_matricula'] ?? 0), "idEstudiante"=>$idEst),JSON_UNESCAPED_UNICODE);
				}elseif($res == "matricula_existente"){
					echo json_encode(array("status"=>false,"msg"=>'Ya está matriculado en esa gestión.'),JSON_UNESCAPED_UNICODE);
				}else{
					echo json_encode(array("status"=>false,"msg"=>'No se pudo rematricular: '.$res),JSON_UNESCAPED_UNICODE);
				}
				die();
			}
		}

		// ITERACIÓN 3 (U-05): comprobante de matrícula imprimible (cabecera + pensiones).
		public function comprobante(int $idMatricula)
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
				die();
			}
			require_once("Models/EstudiantesModel.php");
			$em = new EstudiantesModel();
			$id = intval($idMatricula);
			if($id <= 0){ header("Location:".base_url().'/matricula'); die(); }
			$hist = $em->getHistorialMatricula($id);
			if(empty($hist)){ header("Location:".base_url().'/matricula'); die(); }
			$data['page_tag'] = "Comprobante de matrícula";
			$data['hist'] = $hist;
			$data['emision'] = date('d/m/Y H:i');
			$data['usuario_emisor'] = $_SESSION['userData']['nombre'] ?? '';
			require_once("Views/Matricula/comprobante.php");
			die();
		}

		// BAJA LÓGICA DE MATRÍCULA
		public function delMatricula()
		{
			if ($_POST && $_SESSION['permisosMod']['d']) {
				if(!csrf_check($_POST['csrf_token'] ?? null)){ echo json_encode(array('status'=>false,'msg'=>'Sesión expirada. Recargue la página.'),JSON_UNESCAPED_UNICODE); die(); }
				$intId = intval($_POST['idMatricula'] ?? 0);
				if($intId <= 0){ echo json_encode(array('status'=>false,'msg'=>'ID inválido.'),JSON_UNESCAPED_UNICODE); die(); }
				$res = $this->model->deleteMatricula($intId);
				if ($res === true) {
					$arrResponse = array('status'=>true,'msg'=>'Matrícula dada de baja (pensiones pendientes anuladas).');
				}elseif($res === 'has_pagos'){
					$arrResponse = array('status'=>false,'msg'=>'No se puede dar de baja: tiene pensiones cobradas. Use Retirado/Trasladado en su lugar.');
				}else{
					$arrResponse = array('status'=>false,'msg'=>'Error al eliminar la matrícula.');
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}
	}	

?>