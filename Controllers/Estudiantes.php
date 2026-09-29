<?php
    class Estudiantes extends Controllers{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login']))
			{
				header('Location: '.base_url().'/login');
			}
			getPermisos(3);//Id del módulo en la base de datos Extrayendo los permisos del modulo logueado
		}

		public function index()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
			}
			$data['page_tag'] = "Estudiantes";
			$data['page_title'] = "Estudiantes <small>Marista</small>";
			$data['page_name'] = "Estudiantes";
			$data['page_functions_js'] = "functions_estudiantes.js";
			$this->views->getView($this,"estudiantes",$data);
		}

		// Guarda una foto en Assets/images/uploads/estudiantes/ o retorna null
		private function saveFoto(array $file)
		{
			if(empty($file) || !isset($file['error']) || $file['error'] == UPLOAD_ERR_NO_FILE){
				return [null, null];
			}
			if($file['error'] !== UPLOAD_ERR_OK){
				return [null, 'Error al subir la foto (código '.$file['error'].').'];
			}
			if($file['size'] > 2 * 1024 * 1024){
				return [null, 'La foto supera los 2 MB.'];
			}
			$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
			if(!in_array($ext, ['jpg','jpeg','png','webp'], true)){
				return [null, 'La foto debe ser JPG, PNG o WEBP.'];
			}
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $file['tmp_name']);
			finfo_close($finfo);
			if(!in_array($mime, ['image/jpeg','image/png','image/webp'], true)){
				return [null, 'El archivo no es una imagen válida.'];
			}
			$dir = "Assets/images/uploads/estudiantes/";
			if(!is_dir($dir)){ mkdir($dir, 0755, true); }
			$name = "est_" . date("YmdHis") . "_" . bin2hex(random_bytes(4)) . "." . $ext;
			if(!move_uploaded_file($file['tmp_name'], $dir . $name)){
				return [null, 'No se pudo guardar la foto.'];
			}
			return [$name, null];
		}

		private function validar(array $p, bool $isNew)
		{
			// ITERACIÓN 1 (N-01/N-07): duros mínimos para inscribir todo el año;
			// RUDE/email/celular son DIFERIBLES a 30 días hábiles (no bloquean).
			$req = [
				'txtCi'=>'El C.I. es obligatorio.',
				'txtNombre'=>'El nombre es obligatorio.', 'txtApellido'=>'El apellido es obligatorio.',
				'dateFNacimiento'=>'La fecha de nacimiento es obligatoria.'
			];
			foreach($req as $c=>$msg){ if(empty(trim($p[$c] ?? ''))){ return $msg; } }
			// Diferibles: solo se validan si vienen con valor
			$em = trim(strtolower($p['txtEmail'] ?? ''));
			if($em !== '' && !filter_var($em, FILTER_VALIDATE_EMAIL)){ return 'El email no tiene un formato válido.'; }
			$celRaw = trim($p['txtCelular'] ?? '');
			if($celRaw !== ''){
				$cel = preg_replace('/[^0-9]/','', $celRaw);
				if(strlen($cel) < 7 || strlen($cel) > 9){ return 'El celular debe tener entre 7 y 9 dígitos.'; }
			}
			$fn = strtotime($p['dateFNacimiento']);
			if($fn === false){ return 'Fecha de nacimiento inválida.'; }
			if($fn > time()){ return 'La fecha de nacimiento no puede ser futura.'; }
			if($fn < strtotime('-30 years')){ return 'Verifique la fecha de nacimiento.'; }
			return null;
		}

		// Insertar / actualizar Estudiante
		public function setEstudiante(){
			if(!$_POST){ die(); }
			if(!csrf_check($_POST['csrf_token'] ?? null)){ echo json_encode(array("status"=>false,"msg"=>'Sesión expirada. Recargue la página e intente de nuevo.'), JSON_UNESCAPED_UNICODE); die(); }
			$isNew = strClean($_POST['newStudent'] ?? '1') == "1";
			if($err = $this->validar($_POST, $isNew)){
				echo json_encode(array("status"=>false,"msg"=>$err), JSON_UNESCAPED_UNICODE); die();
			}
			$idEstudiante = intval($_POST['idEstudiante'] ?? 0);
			$strCi = strClean($_POST['txtCi'] ?? '');
			$strRUDE = strClean($_POST['txtRUDE'] ?? '');
			$strlistEst = strClean($_POST['listEst'] ?? 'Nuevo');
			$strNombre = ucwords(strClean($_POST['txtNombre'] ?? ''));
			$strApellido = ucwords(strClean($_POST['txtApellido'] ?? ''));
			$strSex = strClean($_POST['listSexEst'] ?? 'M');
			$strTelefono = preg_replace('/[^0-9]/','', (string)($_POST['txtCelular'] ?? ''));
			$strEmail = strtolower(strClean($_POST['txtEmail'] ?? ''));
			$strDireccion = strClean($_POST['txtDireccion'] ?? '');
			$dateFNacimiento = strClean($_POST['dateFNacimiento']);
			$strPais = strClean($_POST['txtPais'] ?? 'Bolivia');
			$strCiudad = strClean($_POST['txtCiudad'] ?? '');
			$strProvincia = strClean($_POST['txtProvincia'] ?? '');
			$strColegioProc = strClean($_POST['txtColegioProc'] ?? '');
			$strEmergencia = strClean($_POST['txtEmergencia'] ?? '');
			$intStatus = intval($_POST['listStatus'] ?? 1);
			// Solo 1=Activo y 2=Inactivo desde el formulario (0=Eliminado es solo por baja)
			if($intStatus !== 1 && $intStatus !== 2){ $intStatus = 1; }
			$intTipoId = 7;

			// Legajo físico: folio único (vacío = auto en alta, conservar en edición)
			$folioRaw = trim($_POST['txtFolio'] ?? '');
			if($folioRaw !== '' && (!ctype_digit($folioRaw) || intval($folioRaw) <= 0)){
				echo json_encode(array("status"=>false,"msg"=>'El folio debe ser un número mayor a 0 (o vacío para asignarlo).'), JSON_UNESCAPED_UNICODE); die();
			}
			$folio = ($folioRaw === '') ? null : intval($folioRaw);
			$estante = strtoupper(trim(strClean($_POST['txtEstante'] ?? '')));
			$gaveta = trim(strClean($_POST['txtGaveta'] ?? ''));
			$estadoLeg = trim(strClean($_POST['listEstadoLeg'] ?? ''));
			if(!in_array($estadoLeg, ['', 'archivado', 'prestado', 'digitalizado', 'observado'], true)){ $estadoLeg = ''; }
			$estadoLegajo = ($estadoLeg === '') ? null : $estadoLeg;

			// Foto (opcional; en edición null = conservar)
			$fotoName = null; $fotoChanged = false;
			if(!empty($_FILES['fotoEstudiante'])){
				list($fotoName, $fotoErr) = $this->saveFoto($_FILES['fotoEstudiante']);
				if($fotoErr){ echo json_encode(array("status"=>false,"msg"=>$fotoErr), JSON_UNESCAPED_UNICODE); die(); }
				$fotoChanged = ($fotoName !== null);
			}

			if($isNew){
				if(!$_SESSION['permisosMod']['w']){ echo json_encode(array("status"=>false,"msg"=>'Sin permiso para crear.'), JSON_UNESCAPED_UNICODE); die(); }
				// Acceso inicial = C.I. Los cambios de clave son del módulo Usuarios.
				$strPassword = password_hash($strCi, PASSWORD_DEFAULT);
				$uid = intval($_SESSION['idUser'] ?? 0) ?: null; // I3 auditoría
				$request = $this->model->insertEstudiante($strCi,$strRUDE,$strlistEst,$strNombre,$strApellido,$strSex,$strTelefono,$strEmail,$strDireccion,$dateFNacimiento,$strPais,$strCiudad,$strProvincia,$strColegioProc,$strEmergencia,$intTipoId,$strPassword,$intStatus,$fotoName,$folio,$estante !== '' ? $estante : null,$gaveta !== '' ? $gaveta : null,$estadoLegajo,$uid);
				$okMsg = 'Estudiante registrado correctamente.';
			}else{
				if($idEstudiante <= 0){ echo json_encode(array("status"=>false,"msg"=>'ID inválido.'), JSON_UNESCAPED_UNICODE); die(); }
				if(!$_SESSION['permisosMod']['u']){ echo json_encode(array("status"=>false,"msg"=>'Sin permiso para editar.'), JSON_UNESCAPED_UNICODE); die(); }
				// Vacío = el modelo conserva el hash actual (la clave solo se cambia en Usuarios).
				$strPassword = "";
				$uid = intval($_SESSION['idUser'] ?? 0) ?: null; // I3 auditoría
				$request = $this->model->updateEstudiante($idEstudiante,$strCi,$strRUDE,$strlistEst,$strNombre,$strApellido,$strSex,$strTelefono,$strEmail,$strDireccion,$dateFNacimiento,$strPais,$strCiudad,$strProvincia,$strColegioProc,$strEmergencia,$intTipoId,$strPassword,$intStatus,$fotoChanged ? $fotoName : null,$folio,$estante !== '' ? $estante : null,$gaveta !== '' ? $gaveta : null,$estadoLegajo,$uid);
				$okMsg = 'Estudiante actualizado correctamente.';
			}

			if($request == "dato_guardado"){
				// Matricular de una vez (solo en creación)
				$matriculado = "";
				$idMatNuevo = 0;
				if($isNew && !empty($_POST['chkMatricular'])){
					$r = $this->matricularNuevo($strCi, $_POST);
					$matriculado = $r['msg'];
					$idMatNuevo = $r['idMat'];
				}
				// Recupera id para el frontend (útil para redirigir a tutores)
				$idNuevo = 0;
				if($isNew){
					$row = $this->model->select("SELECT id_estudiante FROM estudiante e INNER JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=?", [$strCi]);
					$idNuevo = intval($row["id_estudiante"] ?? 0);
				}
				echo json_encode(array('status'=>true,'msg'=>$okMsg.$matriculado,'id'=>$isNew ? $idNuevo : $idEstudiante,'idMatricula'=>$idMatNuevo,'ci'=>$strCi), JSON_UNESCAPED_UNICODE);
			}elseif(strpos((string)$request, 'exist:') === 0){
				if($fotoName){ @unlink("Assets/images/uploads/estudiantes/".$fotoName); }
				echo json_encode(array("status"=>false,"msg"=>substr($request, 6)), JSON_UNESCAPED_UNICODE);
			}elseif($request == 'exist'){
				if($fotoName){ @unlink("Assets/images/uploads/estudiantes/".$fotoName); }
				echo json_encode(array("status"=>false,"msg"=>'El email, CI, celular o RUDE ya existe.'), JSON_UNESCAPED_UNICODE);
			}else{
				if($fotoName){ @unlink("Assets/images/uploads/estudiantes/".$fotoName); }
				echo json_encode(array("status"=>false,"msg"=>'No es posible almacenar los datos. '.$request), JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		// Matricula inmediata tras crear (usa gestión activa + paralelo elegido)
		// ITERACIÓN 1: soporta Documentación pendiente 30 días hábiles (N-01/F-06).
		// FLUJO-ÓPTIMO: avisa si queda sin tutores y devuelve id de matrícula para el éxito accionable.
		private function matricularNuevo(string $ci, array $post)
		{
			if(empty($_SESSION['permisosMod']['w'])){ return ['msg' => ' (Sin permiso para matricular.)', 'idMat' => 0]; }
			require_once("Models/MatriculaModel.php");
			require_once("Models/GestionModel.php");
			$gm = new GestionModel();
			$act = $gm->selectGestionAct();
			$gestion = intval($act["gestion"] ?? date("Y"));
			$idParalelo = intval($post['listParaleloMat'] ?? 0);
			if($idParalelo <= 0){ return ['msg' => ' (Estudiante creado, pero seleccione un paralelo para matricular.)', 'idMat' => 0]; }
			$mm = new MatriculaModel();
			// ¿Faltan diferibles? -> sugerir pendiente aunque no marquen el check
			$faltaDif = (trim($post['txtRUDE'] ?? '') === '' || trim($post['txtEmail'] ?? '') === '' || trim($post['txtCelular'] ?? '') === '');
			$docPend = !empty($post['chkDocPendiente']) || $faltaDif;
			$estado = $docPend ? 'Pendiente_Documentos' : 'Confirmado';
			$uid = intval($_SESSION['idUser'] ?? 0) ?: null; // REV-Est: auditoría también en matrícula inmediata
			$res = $mm->insertMatricula($ci, $gestion, $idParalelo, strClean($post['listTipoMat'] ?? 'Regular'), '', $estado, $uid);
			if($res == "matricula_guardada"){
				$extra = "";
				$idMat = (int)($mm->select(
					"SELECT m.id_matricula FROM matricula m INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante
					 INNER JOIN persona p ON e.id_persona=p.id_persona
					 WHERE p.ci=? AND m.gestion=? ORDER BY m.id_matricula DESC LIMIT 1", [$ci, $gestion])["id_matricula"] ?? 0);
				if($docPend){
					$plazo = function_exists('plazo30Habiles') ? plazo30Habiles() : date('Y-m-d', strtotime('+30 days'));
					$chk = [
						'ci' => 1, // CI siempre presente (duro)
						'cert_nac' => !empty($post['doc_cert_nac']) ? 1 : 0,
						'rude' => (trim($post['txtRUDE'] ?? '') !== '' ? 1 : (!empty($post['doc_rude']) ? 1 : 0)),
						'solicitud' => !empty($post['doc_solicitud']) ? 1 : 0,
					];
					$comp = !empty($post['chkCompromiso']) || $docPend ? 1 : 0;
					$mm->setDocumentacionByCiGestion($ci, $gestion, $plazo, $chk, $comp, strClean($post['docsObs'] ?? ''), $estado);
					$extra = " Documentación pendiente hasta $plazo (30 días hábiles).";
				}
				// FLUJO-ÓPTIMO (3): apoderado mínimo — avisar, no bloquear (norma)
				try {
					$nt = $mm->select(
						"SELECT COUNT(*) AS c FROM padre pa INNER JOIN estudiante e ON pa.id_estudiante=e.id_estudiante
						 INNER JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=? AND pa.status != 0", [$ci]);
					if(intval($nt['c'] ?? 0) === 0){ $extra .= " ⚠ Sin tutores vinculados: vincúlelos desde Tutores."; }
				} catch (Exception $x) {}
				return ['msg' => " Matriculado en gestión $gestion (10 pensiones generadas).$extra", 'idMat' => $idMat];
			}
			return ['msg' => " (No se pudo matricular: $res)", 'idMat' => 0];
		}

		// Paralelos con cupo para el alta (gestión activa)
		public function paralelosMatricula()
		{
			if($_SESSION['permisosMod']['r']){
				require_once("Models/CursosModel.php");
				require_once("Models/GestionModel.php");
				$gm = new GestionModel();
				$act = $gm->selectGestionAct();
				$gestion = intval($act["gestion"] ?? date("Y"));
				$cm = new CursosModel();
				$arr = $cm->selectCursos($gestion);
				$html = '<option value="0">Seleccione paralelo...</option>';
				foreach(($arr ?: []) as $c){
					$libres = intval($c['cupo']) - intval($c['total_inscritos']);
					if($libres < 0){ $libres = 0; }
					$html .= '<option value="'.$c['id_paralelo'].'"'.($libres<=0?' disabled':'').'>'
						.htmlspecialchars($c['nivel'].': '.$c['grado'].'-'.$c['sigla'].' (libres: '.$libres.')').'</option>';
				}
				echo json_encode(array('status'=>true,'gestion'=>$gestion,'html'=>$html), JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		//Listar Estudiantes registrados
		public function getEstudiantesAll()
		{
			if ($_SESSION['permisosMod']['r']) {
				$arrData = $this->model->selectEstudiantes();
				foreach(($arrData ?: []) as $k=>$v){
					$st = intval($v['status_estudiante']);
					// 1=Activo (verde) · 2=Inactivo (ámbar, visible sin acceso) · 0=Eliminado (oculto por el SP)
					if($st === 1){
						$arrData[$k]['status_estudiante'] = '<span class="badge badge-success">Activo</span>';
					}elseif($st === 2){
						$arrData[$k]['status_estudiante'] = '<span class="badge badge-warning">Inactivo</span>';
					}else{
						$arrData[$k]['status_estudiante'] = '<span class="badge badge-danger">Eliminado</span>';
					}
					// Valor crudo para el filtro (columna oculta): DataTables filtra
					// sobre texto plano, no sobre el HTML del badge.
					$arrData[$k]['estado_raw'] = $st;
					$foto = trim($v['foto'] ?? '');
					if($foto != '' && file_exists("Assets/images/uploads/estudiantes/".$foto)){
						$src = base_url()."/Assets/images/uploads/estudiantes/".$foto;
						$arrData[$k]['foto'] = '<img src="'.$src.'" class="est-avatar" alt="foto" loading="lazy" title="'.htmlspecialchars($v['nombre'].' '.$v['apellido']).'">';
					}else{
						// Avatar con iniciales (color estable por estudiante)
						$ini = mb_strtoupper(mb_substr(trim($v['nombre'] ?? ''),0,1).mb_substr(trim($v['apellido'] ?? ''),0,1));
						$pal = ['#1a3b5d','#2c5282','#8e44ad','#16a085','#b9770e','#2e86c1','#7d3c98'];
						$col = $pal[intval($v['id_estudiante']) % count($pal)];
						$arrData[$k]['foto'] = '<div class="est-avatar est-initials" style="background:'.$col.'" title="'.htmlspecialchars($v['nombre'].' '.$v['apellido']).'">'.$ini.'</div>';
					}
					$nt = intval($v['tutores'] ?? 0);
					$arrData[$k]['tutores'] = $nt > 0
						? '<span class="badge badge-info">'.$nt.' <i class="fa fa-users"></i></span>'
						: '<span class="badge badge-secondary">0</span>';
					$curso = trim($v['curso_actual'] ?? '');
					// ENT_NOQUOTES: escapa <>& pero conserva las comillas del formato
					// 'Primaria 1 "A"' para mostrarlas limpias en el badge.
					$arrData[$k]['curso_actual'] = ($curso != '' && $curso != ' ')
						? '<span class="badge badge-primary">'.htmlspecialchars($curso, ENT_NOQUOTES, 'UTF-8').'</span>'
						: '<span class="text-muted">—</span>';
					// Texto plano para el filtro (columna oculta): inmune a entidades HTML.
					$arrData[$k]['curso_raw'] = ($curso != '' && $curso != ' ') ? $curso : '';
					// Legajo físico: folio cero-rellenado (ordena numérico) + ubicación.
					$folio = $v['folio_fisico'] ?? null;
					$est = trim($v['estante'] ?? '');
					$gav = trim($v['gaveta'] ?? '');
					if($folio !== null && $folio !== ''){
						$ubic = [];
						if($est !== ''){ $ubic[] = 'Est. '.$est; }
						if($gav !== ''){ $ubic[] = 'Gav. '.$gav; }
						$arrData[$k]['legajo'] = '<div class="text-center"><b>'.str_pad((string)intval($folio), 6, '0', STR_PAD_LEFT).'</b>'
							.($ubic ? '<br><small class="text-muted">'.htmlspecialchars(implode(' · ', $ubic), ENT_NOQUOTES, 'UTF-8').'</small>' : '').'</div>';
					}else{
						$arrData[$k]['legajo'] = '<div class="text-center"><span class="text-muted" title="Sin folio asignado">—</span></div>';
					}
					$arrData[$k]['legajo_flag'] = ($folio !== null && $folio !== '') ? 1 : 0;
					$id = intval($v['id_estudiante']);
					$btns = '';
					if($_SESSION['permisosMod']['r']){ $btns .= '<button class="btn btn-info btn-sm" onClick="fntViewEstudiante('.$id.')" title="Ficha del estudiante"><i class="far fa-eye"></i></button> '; }
					if($_SESSION['permisosMod']['u']){ $btns .= '<button class="btn btn-primary btn-sm" onClick="fntEditEstudiante(this,'.$id.')" title="Editar"><i class="fas fa-pencil-alt"></i></button> '; }
					if($_SESSION['permisosMod']['d']){ $btns .= '<button class="btn btn-danger btn-sm" onClick="fntDelEstudiante('.$id.')" title="Dar de baja"><i class="far fa-trash-alt"></i></button> '; }
					// FLUJO: matricular existente sin historial (alta sin chkMatricular)
					if($_SESSION['permisosMod']['w']){ $btns .= '<button class="btn btn-success btn-sm" onClick="fntMatricularExistente(\''.htmlspecialchars($v['ci'] ?? '', ENT_QUOTES).'\')" title="Matricular (primera vez o rematricular)"><i class="fas fa-forward"></i></button> '; }
					$btns .= '<button class="btn btn-warning btn-sm" onClick="fntTutoresEstudiante('.$id.')" title="Ver tutores"><i class="fas fa-users"></i></button>';
					$arrData[$k]['options'] = '<div class="text-center text-nowrap">'.$btns.'</div>';
				}
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		//Ficha 360° de un estudiante
		public function getEstudiante(int $idEstudiante)
		{
			if($_SESSION['permisosMod']['r']){
				$IdEst = intval($idEstudiante);
				if($IdEst > 0)
				{
					$ficha = $this->model->getFicha($IdEst);
					if(empty($ficha))
					{
						$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
					}else{
						$foto = trim($ficha['estudiante']['foto'] ?? '');
						$ficha['foto_url'] = ($foto != '' && file_exists("Assets/images/uploads/estudiantes/".$foto))
							? base_url()."/Assets/images/uploads/estudiantes/".$foto
							: media()."/images/avatar.png";
						$arrResponse = array('status' => true, 'data' => $ficha['estudiante'], 'ficha' => $ficha);
					}
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}

		//Baja segura: solo sin matrículas activas; si no, informa (has_matricula)
		// ITERACIÓN 3 (F-07): búsqueda server-side para autocompletado (top 20).
		public function buscar()
		{
			if($_SESSION['permisosMod']['r']){
				$q = trim(strClean($_GET['q'] ?? ''));
				if(strlen($q) < 2){ echo json_encode(array('status'=>true,'data'=>[]), JSON_UNESCAPED_UNICODE); die(); }
				echo json_encode(array('status'=>true,'data'=>$this->model->buscarEstudiantes($q) ?: []), JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		// ITERACIÓN 3 (N-03): reporte imprimible de rezago 2+ años (Comisión Técnica).
		public function rezagados()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
				die();
			}
			$cand = $this->model->rezagoCandidates() ?: [];
			$rows = [];
			foreach($cand as $c){
				$r = calculaRezago($c['fnacimiento'] ?? '', $c['nivel'] ?? '', $c['grado'] ?? 0);
				if($r['alerta']){ $rows[] = array_merge($c, $r); }
			}
			$data['page_tag'] = "Rezago escolar";
			$data['rows'] = $rows;
			$data['emision'] = date('d/m/Y H:i');
			$data['usuario_emisor'] = $_SESSION['userData']['nombre'] ?? '';
			require_once("Views/Estudiantes/rezagados.php");
			die();
		}

		// ITERACIÓN 3: inclusión/apoyo de un estudiante.
		public function getInclusion(int $idEstudiante)
		{
			if($_SESSION['permisosMod']['r'] && $idEstudiante > 0){
				echo json_encode(array('status'=>true,'data'=>$this->model->getInclusion($idEstudiante)), JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function saveInclusion()
		{
			if(!$_POST){ die(); }
			if(!csrf_check($_POST['csrf_token'] ?? null)){ echo json_encode(array('status'=>false,'msg'=>'Sesión expirada. Recargue la página.'), JSON_UNESCAPED_UNICODE); die(); }
			$idEst = intval($_POST['idEstudianteInc'] ?? 0);
			if($idEst <= 0){ echo json_encode(array('status'=>false,'msg'=>'Estudiante inválido.'), JSON_UNESCAPED_UNICODE); die(); }
			if(!$_SESSION['permisosMod']['u'] && !$_SESSION['permisosMod']['w']){ echo json_encode(array('status'=>false,'msg'=>'Sin permiso.'), JSON_UNESCAPED_UNICODE); die(); }
			$uid = intval($_SESSION['idUser'] ?? 0) ?: null;
			$ok = $this->model->saveInclusion($idEst, [
				'tiene_discapacidad' => !empty($_POST['tieneDisc']),
				'tipo_discapacidad' => strClean($_POST['tipoDisc'] ?? ''),
				'adaptaciones' => strClean($_POST['adaptaciones'] ?? ''),
				'centro_especial' => strClean($_POST['centroEspecial'] ?? ''),
				'matricula_paralela' => !empty($_POST['matParalela']),
				'requiere_comision' => !empty($_POST['reqComision']),
			], $uid);
			echo json_encode($ok ? array('status'=>true,'msg'=>'Inclusión guardada.') : array('status'=>false,'msg'=>'No se pudo guardar.'), JSON_UNESCAPED_UNICODE);
			die();
		}

		//Baja segura
		public function delEstudiante()
		{
			if($_POST && $_SESSION['permisosMod']['d']){
				if(!csrf_check($_POST['csrf_token'] ?? null)){ echo json_encode(array('status'=>false,'msg'=>'Sesión expirada. Recargue la página.'),JSON_UNESCAPED_UNICODE); die(); }
				$intIdEstudiante = intval($_POST['idEstudiante'] ?? 0);
				if($intIdEstudiante <= 0){ echo json_encode(array('status'=>false,'msg'=>'ID inválido.'),JSON_UNESCAPED_UNICODE); die(); }
				$requestDelete = $this->model->deleteEstudiante($intIdEstudiante);
				if($requestDelete === true)
				{
					$arrResponse = array('status' => true, 'msg' => 'Estudiante dado de baja (acceso bloqueado).');
				}elseif($requestDelete === 'has_matricula'){
					$arrResponse = array('status' => false, 'msg' => 'No se puede dar de baja: tiene matrículas activas. Dé de baja la matrícula primero.');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el Estudiante.');
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		// Hoja imprimible: historial de pagos de UNA matrícula
		public function historial(int $idMatricula)
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
				die();
			}
			$id = intval($idMatricula);
			if($id <= 0){ header("Location:".base_url().'/estudiantes'); die(); }
			$hist = $this->model->getHistorialMatricula($id);
			if(empty($hist)){ header("Location:".base_url().'/estudiantes'); die(); }
			$data['page_tag'] = "Historial de pagos";
			$data['hist'] = $hist;
			$data['emision'] = date('d/m/Y H:i');
			$data['usuario_emisor'] = $_SESSION['userData']['nombre'] ?? '';
			require_once("Views/Estudiantes/historial.php");
			die();
		}

		// Carnet estudiantil con QR de verificación (hoja imprimible)
		public function carnet(int $idEstudiante)
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
				die();
			}
			$id = intval($idEstudiante);
			if($id <= 0){ header("Location:".base_url().'/estudiantes'); die(); }
			$ficha = $this->model->getFicha($id);
			if(empty($ficha)){ header("Location:".base_url().'/estudiantes'); die(); }
			$foto = trim($ficha['estudiante']['foto'] ?? '');
			$data['page_tag'] = "Carnet estudiantil";
			$data['ficha'] = $ficha;
			$data['foto_url'] = ($foto != '' && file_exists("Assets/images/uploads/estudiantes/".$foto))
				? base_url()."/Assets/images/uploads/estudiantes/".$foto : '';
			$data['verify_url'] = qrVerifyEstUrl($id);
			// Matrícula más reciente para curso/gestión del carnet
			$mat = null;
			foreach(($ficha["matriculas"] ?: []) as $mm){
				if($mat === null || intval($mm["gestion"]) > intval($mat["gestion"])){ $mat = $mm; }
			}
			$data['matricula'] = $mat;
			require_once("Views/Estudiantes/carnet.php");
			die();
		}
}
?>
