<?php
    class Pensiones extends Controllers{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login']))
			{
				header('Location: '.base_url().'/login');
			}
			getPermisos(5);//Id del módulo en la base de datos Extrayendo los permisos del modulo logueado
		}

		public function index()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/Pensiones');
			}
			$data['page_tag'] = "Mensualidades";
			$data['page_title'] = "Mensualidades <small>Pagos de estudiantes</small>";
			$data['page_name'] = "Mensualidades";
			$data['page_functions_js'] = "functions_pensiones.js";
			$this->views->getView($this,"pensiones",$data);
		}

        public function listPensionesPagadas()
		{
			if ($_SESSION['permisosMod']['r']) {
				$arrData = $this->model->selectPensionesPagadas();
                for ($i=0; $i < count($arrData); $i++) {
                    $btnView = '';
                    $btnDelete = '';

                    if($arrData[$i]['status_Pension'] == 1)
                    {
                        $arrData[$i]['status_Pension'] = '<span class="badge badge-success">Activo</span>';
                    }else{
                        $arrData[$i]['status_Pension'] = '<span class="badge badge-danger">Inactivo</span>';
                    }
					$arrData[$i]['Estado_Pago'] = '<span class="badge badge-success px-3">'.$arrData[$i]['Estado_Pago'].'</span>';
					$arrData[$i]['nro_recibo'] = $arrData[$i]['nro_recibo'] !== null
						? '<b>'.str_pad((string)$arrData[$i]['nro_recibo'], 6, '0', STR_PAD_LEFT).'</b>'
						: '<span class="text-muted">—</span>';
					$arrData[$i]['cajero'] = trim($arrData[$i]['cajero'] ?? '') !== ''
						? htmlspecialchars($arrData[$i]['cajero']) : '<span class="text-muted">—</span>';


                    if($_SESSION['permisosMod']['r']){
                        $btnView = '<button class="btn btn-info btn-sm" onClick="fntViewPension('.$arrData[$i]['id_pension'].')" title="Ver recibo"><i class="far fa-eye"></i></button>';
                    }
                    // La mensualidad pagada no se edita: solo ver recibo o anular pago
                    if($_SESSION['permisosMod']['d']){
                        $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelPension('.$arrData[$i]['id_pension'].')" title="Anular pago"><i class="fas fa-ban"></i></button>';

                    }
                    $arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnDelete.'</div>';
                }
                echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}
        	die();
        }

		public function getPensionesEst()
		{
			if ($_POST) {
				if ($_SESSION['permisosMod']['r']) {
					
					$CiEstudiante=strClean($_POST['txtCiEstudiante']);
					$html='';
					if ($CiEstudiante	==	'') {
                        $arrResponse = array('status' => false, 'msg' => 'Datos no recibidos');
					}else {
							
						$arrData = $this->model->selectPensionesEstudiantes($CiEstudiante);
						if ($arrData) {
							for ($i=0; $i < count($arrData); $i++) {
						
								$btnView = '';
								$btnEdit = '';
								$btnDelete = '';
								$btnImprimir = '';
	
								
	
								if($_SESSION['permisosMod']['r']){
									$btnView = '<button class="btn btn-info btn-sm btnViewMateria" onClick="fntViewPension('.$arrData[$i]['id_pensiones'].')" title="Ver recibo"><i class="far fa-eye"></i></button>';
								}
								if($_SESSION['permisosMod']['u']){
									if ($arrData[$i]['estado_pago'] == 1) {
										$btnEdit = '';
									}else {
										$btnEdit = '<button class="btn btn-success  btn-sm btnEditMateria" onClick="fntPagarPension('.$arrData[$i]['id_pensiones'].')" title="Pagar Mensualidad"><i class="fa fa-money"></i></button>';
									}
								}
								// Anular solo tiene sentido sobre pagos ya realizados
								if($_SESSION['permisosMod']['d'] && $arrData[$i]['estado_pago'] == 1){
									$btnDelete = '<button class="btn btn-danger btn-sm btnDelMateria" onClick="fntAnularPension('.$arrData[$i]['id_pensiones'].')" title="Anular pago"><i class="fas fa-ban"></i></button>';

								}

								// Estado: pagado / vencido / pendiente (con fecha de vencimiento)
								if($arrData[$i]['estado_pago'] == 1)
								{
									$arrData[$i]['estado_pago'] = '<span class="badge badge-success m-1 px-3">Pagado</span>';
									$btnImprimir = '<button class="btn btn-secundary border-dark btn-sm btnViewMateria" onClick="fntImprimirRecibo('.$arrData[$i]['id_pensiones'].')" title="Imprimir"><i class="fa fa-print"></i></button>';

								}elseif(!empty($arrData[$i]['vencida'])){
									$arrData[$i]['estado_pago'] = '<span class="badge badge-danger m-1 px-3">Vencido</span><br><small class="text-muted">venció '.$arrData[$i]['fecha_vencimiento'].'</small>';
								}else{
									$arrData[$i]['estado_pago'] = '<span class="badge badge-warning m-1 px-3">Pendiente</span><br><small class="text-muted">vence '.$arrData[$i]['fecha_vencimiento'].'</small>';
								}
							$arrData[$i]['options'] = '<div class="text-center">'.$btnImprimir.' '.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
							$nro = $arrData[$i]['nro_recibo'] ?? null;
							$celdaRecibo = ($nro !== null && $nro !== '')
								? '<b>'.str_pad((string)$nro, 6, '0', STR_PAD_LEFT).'</b>'
								: '<span class="text-muted">—</span>';

							$html.='<tr >
										<td>'.$arrData[$i]['matricula'].'</td>
										<td>'.$arrData[$i]['gestion_academica'].'</td>
										<td>'.$arrData[$i]['curso'].'</td>
										<td>'.$arrData[$i]['mes_pago'].'</td>
										<td>'.htmlspecialchars($arrData[$i]['fecha_vencimiento'] ?? '—').'</td>
										<td>'.$arrData[$i]['monto_a_pagar'].'</td>
										<td>'.$arrData[$i]['estado_pago'].'</td>
										<td class="text-center">'.$celdaRecibo.'</td>
										<td>
											'.$arrData[$i]['options'].'
										</td>
									</tr>';
							}
							$CiEstudiante=$arrData[0]['ci_estudiante'];
							$NombreEstudiante=$arrData[0]['nombre_completo'];
							$arrResponse = array('status' => true,'msg'=>'Datos encontrados' ,'ci'=>$CiEstudiante,'nombre'=>$NombreEstudiante,'tabla'=>$html);
						}else {
							$arrResponse = array('status' => false, 'msg' => 'No se encontró datos del estudiante');					
						}
					} 
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				}
			}die();
			
		}

		// Metodo de pago de mensualidad (monto exacto, folio de recibo automático)
		public function setPagoPension()
		{
			if ($_POST)
			{
				if (empty($_POST['txtMonto'])|| empty($_POST['listTipoPago'])||empty($_POST['txtNameAportante'])||empty($_POST['txtLastAportante'])||empty($_POST['txtCiAportante'])||empty($_POST['txtParentesco'])||empty($_POST['intIdPension'])) {
                    $arrResponse=array("status"=>false,"msg"=>'No se recibieron los datos Correctamente');
				}else {

					$txtMonto=floatval(strClean($_POST['txtMonto']));
					$listTipoPago=strClean($_POST['listTipoPago']);
					$txtCodigo=strClean($_POST['txtCodigo'] ?? '');
					$txtNameAportante=ucwords(strClean($_POST['txtNameAportante']));
					$txtLastAportante=ucwords(strClean($_POST['txtLastAportante']));
					$txtCiAportante=strClean($_POST['txtCiAportante']);
					$txtParentesco=ucwords(strClean($_POST['txtParentesco']));
					$intIdPension=intval(strClean($_POST['intIdPension']));
					$idUsuario=intval($_SESSION['idUser'] ?? 0);
					$request_user='';

					if ($_SESSION['permisosMod']['w']) {
						$request_user=$this->model->setPago($txtMonto,$listTipoPago,$txtCodigo,$txtNameAportante,$txtLastAportante,$txtCiAportante,$txtParentesco,$intIdPension,$idUsuario);
					}


                    // El modelo retorna array con folio o string de error
                    if (is_array($request_user) && ($request_user["status"] ?? '') == "Pagado") {
                        $arrResponse=array("status"=>true,"msg"=>'Mensualidad pagada. Recibo N° '.str_pad((string)$request_user["recibo"], 6, '0', STR_PAD_LEFT).'.',"recibo"=>$request_user["recibo"]);

                    }else {
						$arrResponse=array("status"=>false,"msg"=>'No es posible realizar el pago: '.(is_array($request_user) ? 'error' : $request_user));
                    }
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				die();
			}
		}

		// Traer dato de pension
		public function getDatoIdPension($id_pension)
		{
			if ($_SESSION['permisosMod']['w']) {
				$idPension=intval(strClean($id_pension));
				$request_user='';

				if ($idPension>0) {
					$request_user=$this->model->selectPensionDate($idPension);
					if ($request_user) {
                        $arrResponse=array("status"=>true,"data"=>$request_user);
					}else {
                        $arrResponse=array("status"=>false,"msg"=>'No hay datos');
					}
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                	die();
				}
			}
		}
		// Legacy: reporte de prueba eliminado. Usar recibos($id) para comprobantes reales.
		public function generateRecibo()
		{
			header("Location:".base_url().'/pensiones');
			die();
		}

		// Anular un pago con motivo auditado (vuelve la mensualidad a pendiente)
		public function anularPension()
		{
			if ($_POST && $_SESSION['permisosMod']['d']) {
				$intId = intval($_POST['idPension'] ?? 0);
				$motivo = trim(strClean($_POST['motivo'] ?? ''));
				if($intId <= 0){ echo json_encode(array('status'=>false,'msg'=>'ID inválido.'),JSON_UNESCAPED_UNICODE); die(); }
				if($motivo === ''){ echo json_encode(array('status'=>false,'msg'=>'Indique el motivo de la anulación.'),JSON_UNESCAPED_UNICODE); die(); }
				$res = $this->model->anularPension($intId, $motivo, intval($_SESSION['idUser'] ?? 0));
				if($res === "Anulado"){
					$arrResponse = array('status'=>true,'msg'=>'Pago anulado, la mensualidad vuelve a pendiente.');
				}else{
					$arrResponse = array('status'=>false,'msg'=>$res);
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		// Morosidad: mensualidades vencidas ordenadas por antigüedad
		public function morosidad()
		{
			if($_SESSION['permisosMod']['r']){
				$arrData = $this->model->morosidad();
				foreach(($arrData ?: []) as $k=>$v){
					$arrData[$k]['estado'] = '<span class="badge badge-danger">Vencido hace '.$v['dias_atraso'].' día(s)</span>';
					$arrData[$k]['monto'] = 'Bs. '.number_format((float)$v['monto'], 2);
					$id = intval($v['id_pensiones']);
					$btn = '';
					if($_SESSION['permisosMod']['u']){
						$btn = '<button class="btn btn-success btn-sm" onClick="fntPagarPension('.$id.')" title="Pagar"><i class="fa fa-money"></i></button>';
					}
					$arrData[$k]['options'] = '<div class="text-center">'.$btn.'</div>';
				}
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		// KPIs de la cabecera
		public function resumen()
		{
			if($_SESSION['permisosMod']['r']){
				echo json_encode(array('status'=>true,'data'=>$this->model->resumen()),JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		// Serie mensual para gráficos (gestión activa)
		public function serie()
		{
			if($_SESSION['permisosMod']['r']){
				echo json_encode(array('status'=>true,'data'=>$this->model->serieMensual()),JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function recibos($idPension, $formato = '')
		{
			if(empty($_SESSION['permisosMod']['w'])){
				header("Location:".base_url().'/Pensiones');
			}
			$pension = intval(strclean($idPension));
			$formato = strtolower(trim(strclean($formato)));
			if(!in_array($formato, ['', 'carta', 'termica'], true)){ $formato = ''; }
			$Colegio = $this->model->getColegio(1); //1 es el id del colegio marista 1
			$datePension = $this->model->selectPensionDate($pension);
			if(empty($datePension)){ header("Location:".base_url().'/Pensiones'); die(); }
			$nro = intval($datePension['nro_recibo'] ?? 0);
			$arrData = array("pension"=>$datePension,"colegio"=>$Colegio);
			$data['page_tag'] = "Recibo";
			$data['page_title'] = "Recibo";
			$data['page_name'] = "Recibo";
			$data['recibo'] = $arrData;
			$data['formato'] = $formato;
			// QR solo si el pago sigue vigente (con folio); si se anuló, no verifica
			$data['verify_url'] = ($nro > 0 && intval($datePension['estado_pago']) === 1)
				? qrVerifyUrl($nro) : '';
			$this->views->getView($this,"recibo",$data);
		}
    }

?>
