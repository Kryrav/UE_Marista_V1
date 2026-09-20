<?php 

	class Cursos extends Controllers{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login']))
			{
				header('Location: '.base_url().'/login');
			}
			getPermisos(7);
		}

		public function index()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
			}
			$data['page_tag'] = "Cursos";
			$data['page_title'] = "Cursos <small></small>";
			$data['page_name'] = "Cursos";
			$data['page_functions_js'] = "functions_cursos.js";
			$this->views->getView($this,"cursos",$data);
		}

		public function listarCursos($gestion)
		{
			if ($_SESSION['permisosMod']['r']) {
				$intGestion=intval($gestion);
				if($intGestion < 2000 || $intGestion > 2100){ $intGestion = intval(date("Y")); }
				$arrData = $this->model->selectCursos($intGestion);
				if(empty($arrData)){
					echo '<div class="col-12"><div class="curso-empty">'
						.'<i class="fa fa-folder-open fa-3x"></i>'
						.'<h5>Sin paralelos en la gestión '.$intGestion.'</h5>'
						.'<p class="mb-0">Cambia de gestión o registra un nuevo paralelo.</p>'
						.'</div></div>';
					die();
				}
				$htmlCard='';
                for ($i=0; $i < count($arrData); $i++) {
                    $btnView = '';
                    $btnEdit = '';
                    $btnDelete = '';

                    $estado = intval($arrData[$i]['status'] ?? 0);
                    $badge = $estado == 1
                        ? '<span class="badge badge-success curso-estado">Activo</span>'
                        : '<span class="badge badge-danger curso-estado">Inactivo</span>';
					$total = intval($arrData[$i]['total_inscritos'] ?? 0);
					$cupo = intval($arrData[$i]['cupo'] ?? 0);
					$nivel = trim($arrData[$i]['nivel'] ?? '');
					$grado = trim($arrData[$i]['grado'] ?? '');
					$sigla = trim($arrData[$i]['sigla'] ?? '');
					$turno = trim($arrData[$i]['turno'] ?? '');
					$tutor = trim($arrData[$i]['tutor'] ?? '');
					if($tutor == ''){ $tutor = 'Sin asignación'; }
					$idPar = intval($arrData[$i]['id_paralelo']);

					// Monograma y título legible (Inicial no usa grado numérico)
					$esInicial = (strcasecmp($nivel, 'Inicial') == 0);
					$gradoTxt = $esInicial ? 'Inicial' : ($grado.'°');
					$mono = $esInicial ? ('IN-'.$sigla) : ($grado.'°'.$sigla);
					$pct = $cupo > 0 ? min(100, round($total / $cupo * 100)) : 0;
					if($total >= $cupo && $cupo > 0){
						$barClass = 'bg-danger'; $ocuClass = 'full'; $ocuTxt = 'Cupo lleno';
					}elseif($pct >= 85){
						$barClass = 'bg-warning'; $ocuClass = 'warn'; $ocuTxt = 'Últimos cupos';
					}else{
						$barClass = 'bg-success'; $ocuClass = 'ok'; $ocuTxt = 'Disponible';
					}
					$cardClass = $estado == 1 ? '' : ' curso-inactivo';
					$search = strtolower($nivel.' '.$grado.' '.$sigla.' '.$tutor.' '.$turno);

					$card='<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4 curso-item"'
								.' data-nivel="'.htmlspecialchars($nivel).'"'
								.' data-estado="'.$estado.'"'
								.' data-search="'.htmlspecialchars($search).'"'
								.' data-inscritos="'.$total.'"'
								.' data-cupo="'.$cupo.'">'
								.'<div class="card curso-card h-100'.$cardClass.'">'
									.'<div class="curso-banner">'
										.'<div class="curso-monogram">'.htmlspecialchars($mono).'</div>'
										.'<div class="curso-banner-info">'
											.'<span class="curso-nivel">'.htmlspecialchars($nivel.' '.$gradoTxt.' "'.$sigla.'"').'</span>'
											.'<span class="curso-turno"><i class="fa fa-clock-o"></i> '.htmlspecialchars($turno).'</span>'
										.'</div>'
										.$badge
									.'</div>'
									.'<div class="card-body">'
										.'<div class="curso-meta'.($tutor == 'Sin asignación' ? ' muted' : '').'"><i class="fa fa-user"></i><span>'.htmlspecialchars($tutor).'</span></div>'
										.'<div class="d-flex justify-content-between align-items-baseline">'
											.'<small class="curso-cupo-label">Inscritos</small>'
											.'<small class="curso-cupo-label"><b>'.$total.'</b> / '.$cupo.'</small>'
										.'</div>'
										.'<div class="progress"><div class="progress-bar '.$barClass.'" role="progressbar" style="width: '.$pct.'%" aria-valuenow="'.$pct.'" aria-valuemin="0" aria-valuemax="100"></div></div>'
										.'<small class="curso-ocupacion '.$ocuClass.'">'.$pct.'% · '.$ocuTxt.'</small>'
									.'</div>'
									.'<div class="card-footer">';

                    if($_SESSION['permisosMod']['r']){
                        $btnView = '<button class="btn btn-info btn-sm btn-block" onClick="fntViewCurso('.$idPar.','.$intGestion.')" title="Ver estudiantes del curso"><i class="fa fa-eye"></i> Ver curso</button>';
                    }
                    if($_SESSION['permisosMod']['u']){
                        $btnEdit = '<button class="btn btn-outline-primary btn-sm" onClick="fntEditCurso(this,'.$idPar.')" title="Editar paralelo"><i class="fas fa-pencil-alt"></i> Editar</button>';
                    }
                    if($_SESSION['permisosMod']['d']){
                        $btnDelete = '<button class="btn btn-outline-danger btn-sm" onClick="fntDelCurso('.$idPar.')" title="Dar de baja el paralelo"><i class="fa fa-trash-alt"></i> Baja</button>';
                    }

					$card.= $btnView;
					if($btnEdit != '' || $btnDelete != ''){
						$card.= '<div class="curso-actions">'.$btnEdit.' '.$btnDelete.'</div>';
					}
					$card.='</div></div></div>';
					$htmlCard.=$card;
                }
				echo $htmlCard;
			}
			die();
		}

		public function viewCurso($idParalelo, $gestion = 0)
		{
			// Compat: acepta "id,gestion" o dos segmentos URL
			if (strpos((string)$idParalelo, ",") !== false) {
				$partes = explode(",", (string)$idParalelo);
				$idParalelo = intval($partes[0]);
				$gestion = intval($partes[1] ?? 0);
			}else{
				$idParalelo = intval($idParalelo);
				$gestion = intval($gestion);
			}
			if ($gestion <= 2000) { $gestion = intval(date("Y")); }
			if($_SESSION['permisosMod']['r']){
                if($idParalelo > 0 && $gestion > 2000 ) //Validamos que tengamos un ID válido
                {
					$datoCurso='';
					$ListaCurso='';
					// Consultamos datos del curso (el SP devuelve fila de NULLs si no existe por el COUNT agregado)
                    $arrData = $this->model->selectdatosCurso($idParalelo,$gestion);
					$existeCurso = !empty($arrData) && !empty($arrData[0]['id_paralelo']);
					if(!$existeCurso)
                    {
                        $arrResponse = array('statusLista' => false, 'msg' => 'No hay datos del curso.');
						echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
                    }else{
						$estadoPar = intval($arrData[0]['status_paralelo'] ?? $arrData[0]['status'] ?? 0);
						$arrData[0]['status_paralelo'] = $estadoPar == 1 ? 'ACTIVO' : 'INACTIVO';
						// Normaliza conteo por si el SP no lo trae
						if(!isset($arrData[0]['total_inscritos'])){ $arrData[0]['total_inscritos'] = 0; }
                        $datoCurso = $arrData;
                    }
					// Consultamos estudiantes del paralelo (SP devuelve ci/nombre/apellido/email/cel/status)
					$arrData = $this->model->selectEstudiantesCurso($idParalelo,$gestion);
					if(empty($arrData))
                    {
                        $arrResponse = array('statusLista' => true, 'dataCurso' => $datoCurso, 'listaCurso' => '', 'msg' => 'No hay estudiantes inscritos.');
						echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);die();
                    }else{

						for ($i=0; $i < count($arrData); $i++) {
							$btnView = '';
							$card='';
							$idEst = intval($arrData[$i]['id_estudiante'] ?? 0);
							$ci = htmlspecialchars($arrData[$i]['ci'] ?? $arrData[$i]['ci_pers'] ?? '');
							$mat = intval($arrData[$i]['id_matricula'] ?? 0);
							$nom = htmlspecialchars($arrData[$i]['nombre'] ?? $arrData[$i]['nombre_pers'] ?? '');
							$ape = htmlspecialchars($arrData[$i]['apellido'] ?? $arrData[$i]['apellido_pers'] ?? '');
							$ema = htmlspecialchars($arrData[$i]['email'] ?? $arrData[$i]['email_pers'] ?? '');
							$cel = htmlspecialchars($arrData[$i]['cel'] ?? $arrData[$i]['celular_pers'] ?? '');
							$st = intval($arrData[$i]['status'] ?? $arrData[$i]['status_estudiante'] ?? 0);
							if($_SESSION['permisosMod']['r'] && $idEst > 0){
								$btnView = '<button class="btn btn-info btn-sm btn-block mb-2" onClick="fntViewEstudiante('.$idEst.')" title="Ver Estudiante">Ver Estudiante</button>';
							}
							$badge = $st == 1
								? '<span class="badge badge-success">Activo</span>'
								: '<span class="badge badge-danger">Inactivo</span>';
							$card='	<tr>
										<td>'.$ci.'</td>
										<td>'.$mat.'</td>
										<td>'.$nom.'</td>
										<td>'.$ape.'</td>
										<td>'.$ema.'</td>
										<td>'.$cel.'</td>
										<td>'.$badge.'</td>
										<td>'.$btnView.'</td>
									</tr> ';
							$ListaCurso.=$card;

						}

                    }
					$arrResponse = array('statusLista' => true, 'dataCurso' =>$datoCurso ,'listaCurso' => $ListaCurso);
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                }
            }
		}

		public function listParalelos($gestion)
		{		
			$intGestion=intval($gestion);
			$arrData = $this->model->selectCursos($intGestion);
			$html='';
			if ($arrData) {
				for ($i=0; $i < count($arrData); $i++) { 
					$espacios=0;
					$espacios=$arrData[$i]['cupo'] - $arrData[$i]['total_inscritos'];
					$html.='<option value="'.$arrData[$i]['id_paralelo'].'">'.$arrData[$i]['nivel'].': '.$arrData[$i]['grado'].'-'.$arrData[$i]['sigla'].'  / Cupo: '.$espacios.'</option>';
				}
				
				
			}
		
			echo $html;
			
 	   }

		// Hoja imprimible directa: nómina del curso (se auto-imprime al abrir)
		public function lista($idParalelo, $gestion = 0)
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
				die();
			}
			$idParalelo = intval($idParalelo);
			$gestion = intval($gestion);
			if ($gestion <= 2000) { $gestion = intval(date("Y")); }
			if($idParalelo <= 0){ header("Location:".base_url().'/cursos'); die(); }
			$lista = $this->model->selectListaImprimir($idParalelo, $gestion);
			if(empty($lista["curso"])){ header("Location:".base_url().'/cursos'); die(); }
			$data['page_tag'] = "Nómina";
			$data['lista'] = $lista;
			$data['gestion'] = $gestion;
			$data['emision'] = date('d/m/Y H:i');
			require_once("Views/Cursos/lista_imprimir.php");
			die();
		}

		public function s($idParalelo, $gestion = 0)
		{
			
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
			}
			if (strpos((string)$idParalelo, ",") !== false) {
				$partes = explode(",", (string)$idParalelo);
				$idParalelo = intval($partes[0]);
				$gestion = intval($partes[1] ?? 0);
			}else{
				$idParalelo = intval($idParalelo);
				$gestion = intval($gestion);
			}
			if ($gestion <= 2000) { $gestion = intval(date("Y")); }

			$datoCurso='';
			$arrData = $this->model->selectdatosCurso($idParalelo,$gestion);
			$existeCurso = !empty($arrData) && !empty($arrData[0]['id_paralelo']);
			if(!$existeCurso){
				header("Location:".base_url().'/cursos');
				die();
			}
			$estadoPar = intval($arrData[0]['status_paralelo'] ?? $arrData[0]['status'] ?? 0);
			$arrData[0]['status_paralelo'] = $estadoPar == 1 ? 'ACTIVO' : 'INACTIVO';
			if(!isset($arrData[0]['total_inscritos'])){ $arrData[0]['total_inscritos'] = 0; }
			$datoCurso = $arrData[0]; //DATOS DEL CURSO
  			$arrData = $this->model->selectEstudiantesCurso($idParalelo,$gestion);
			$ListaCurso=''; //LISTA DE ESTUDIANTE
			if(!empty($arrData)){
			for ($i=0; $i < count($arrData); $i++) {
				$ci = htmlspecialchars($arrData[$i]['ci'] ?? $arrData[$i]['ci_pers'] ?? '');
				$mat = intval($arrData[$i]['id_matricula'] ?? 0);
				$nom = htmlspecialchars($arrData[$i]['nombre'] ?? $arrData[$i]['nombre_pers'] ?? '');
				$ape = htmlspecialchars($arrData[$i]['apellido'] ?? $arrData[$i]['apellido_pers'] ?? '');
				$ema = htmlspecialchars($arrData[$i]['email'] ?? $arrData[$i]['email_pers'] ?? '');
				$cel = htmlspecialchars($arrData[$i]['cel'] ?? $arrData[$i]['celular_pers'] ?? '');
				$st = intval($arrData[$i]['status'] ?? $arrData[$i]['status_estudiante'] ?? 0);
				$badge = $st == 1
					? '<span class="badge badge-success">Activo</span>'
					: '<span class="badge badge-danger">Inactivo</span>';
				$card='	<tr>
							<td>'.$ci.'</td>
							<td>'.$mat.'</td>
							<td>'.$nom.'</td>
							<td>'.$ape.'</td>
							<td>'.$ema.'</td>
							<td>'.$cel.'</td>
							<td>'.$badge.'</td>
						</tr> ';
				$ListaCurso.=$card;

			}
			}
			$data['page_tag'] = "Cursos";
			$data['page_title'] = "Cursos <small></small>";
			$data['page_name'] = "Cursos";
			$data['page_functions_js'] = "functions_cursos.js";
			$data['curso']=$datoCurso;
			$data['estudiante']=$ListaCurso;
			// dep($data);
			$this->views->getView($this,"listaEstudiante",$data);
		}

		// Obtener un paralelo para ver/editar
		public function getParalelo(int $id)
		{
			if ($_SESSION['permisosMod']['r'] && $id > 0) {
				$arrData = $this->model->selectParalelo($id);
				if (empty($arrData)) {
					echo json_encode(array('status'=>false,'msg'=>'Datos no encontrados.'),JSON_UNESCAPED_UNICODE);
				}else{
					echo json_encode(array('status'=>true,'data'=>$arrData),JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}

		// Docentes activos para el selector de tutor (solo planta docente)
		public function docentesTutores()
		{
			if($_SESSION['permisosMod']['r']){
				require_once("Models/DocentesModel.php");
				$dm = new DocentesModel();
				$html = '<option value="0">Sin asignación</option>';
				foreach(($dm->optionsActivos() ?: []) as $d){
					$html .= '<option value="'.$d['id_persona'].'">'.htmlspecialchars($d['nombre_completo']).'</option>';
				}
				echo $html;
			}
			die();
		}

		// Crear o actualizar paralelo (el tutor solo puede ser docente activo)
		public function saveParalelo()
		{
			if (!$_POST) { die(); }
			$id = intval($_POST['idParalelo'] ?? 0);
			$rawGrado = strClean($_POST['listGrado'] ?? '');
			if (strtolower($rawGrado) === 'inicial') {
				$nivel = 'Inicial'; $grado = 0;
			}else{
				$nivel = strClean($_POST['listNivel'] ?? 'Primaria');
				if ($nivel == '' || $nivel == '1') { $nivel = 'Primaria'; }
				$grado = intval($rawGrado);
			}
			$sigla = strClean($_POST['listSigla'] ?? 'A');
			$rawTurno = strClean($_POST['listTurno'] ?? 'M');
			$turno = ($rawTurno === 'T' || strtolower($rawTurno) === 'tarde') ? 'Tarde' : 'Mañana';
			// Tutor: solo docentes activos de la unidad (por id); 0 = Sin asignación
			require_once("Models/DocentesModel.php");
			$dm = new DocentesModel();
			$tutor = $dm->nombreTutor(intval($_POST['listTutorDocente'] ?? 0));
			if($tutor === null){
				echo json_encode(array('status'=>false,'msg'=>'El tutor debe ser un docente activo de la unidad educativa.'),JSON_UNESCAPED_UNICODE); die();
			}
			$cupo = intval($_POST['intCupo'] ?? 30);
			if ($cupo <= 0) { $cupo = 30; }
			$status = intval($_POST['status'] ?? $_POST['TipoEstudiante'] ?? 1);
			if ($status != 0 && $status != 1) { $status = 1; }

			if ($id > 0) {
				if (!$_SESSION['permisosMod']['u']) { echo json_encode(array('status'=>false,'msg'=>'Sin permiso.'),JSON_UNESCAPED_UNICODE); die(); }
				$res = $this->model->updateParalelo($id,$nivel,$grado,$sigla,$cupo,$tutor,$turno,$status);
			}else{
				if (!$_SESSION['permisosMod']['w']) { echo json_encode(array('status'=>false,'msg'=>'Sin permiso.'),JSON_UNESCAPED_UNICODE); die(); }
				$res = $this->model->insertParalelo($nivel,$grado,$sigla,$cupo,$tutor,$turno,$status);
			}
			if ($res == "dato_guardado") {
				echo json_encode(array('status'=>true,'msg'=> $id > 0 ? 'Paralelo actualizado.' : 'Paralelo creado.'),JSON_UNESCAPED_UNICODE);
			}else{
				echo json_encode(array('status'=>false,'msg'=>'No se pudo guardar: '.$res),JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function delParalelo()
		{
			if ($_POST && $_SESSION['permisosMod']['d']) {
				$id = intval($_POST['idParalelo'] ?? 0);
				if ($id > 0 && $this->model->deleteParalelo($id)) {
					echo json_encode(array('status'=>true,'msg'=>'Paralelo dado de baja.'),JSON_UNESCAPED_UNICODE);
				}else{
					echo json_encode(array('status'=>false,'msg'=>'Error al eliminar.'),JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}
	}
?>

