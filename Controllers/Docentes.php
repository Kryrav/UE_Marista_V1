<?php
    class Docentes extends Controllers{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){ header('Location: '.base_url().'/login'); }
			getPermisos(8);
		}
		public function index()
		{
			if(empty($_SESSION['permisosMod']['r'])){ header("Location:".base_url().'/dashboard'); }
			$data['page_tag'] = "Docentes";
			$data['page_title'] = "Docentes <small>Planta docente</small>";
			$data['page_name'] = "Docentes";
			$data['page_functions_js'] = "functions_docentes.js";
			$this->views->getView($this,"docentes",$data);
		}
		public function getDocentes()
		{
			if ($_SESSION['permisosMod']['r']) {
				$arrData = $this->model->selectDocentes();
				foreach($arrData as $k=>$v){
					$arrData[$k]['status'] = \Services\Presenter::estado(intval($v['status'] ?? 0));
					$bv='';$be='';$bd='';
					if($_SESSION['permisosMod']['r']){ $bv='<button class="btn btn-info btn-sm" onClick="fntViewDocente('.$v['id_persona'].')"><i class="far fa-eye"></i></button>'; }
					if($_SESSION['permisosMod']['u']){ $be='<button class="btn btn-primary btn-sm" onClick="fntEditDocente('.$v['id_persona'].')"><i class="fas fa-pencil-alt"></i></button>'; }
					if($_SESSION['permisosMod']['d']){ $bd='<button class="btn btn-danger btn-sm" onClick="fntDelDocente('.$v['id_persona'].')"><i class="far fa-trash-alt"></i></button>'; }
					$arrData[$k]['options']='<div class="text-center">'.$bv.' '.$be.' '.$bd.'</div>';
				}
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}
			die();
		}
		public function getDocente(int $id)
		{
			if($_SESSION['permisosMod']['r'] && $id>0){
				$d=$this->model->selectDocente($id);
				echo json_encode(empty($d)?array('status'=>false,'msg'=>'No encontrado'):array('status'=>true,'data'=>$d),JSON_UNESCAPED_UNICODE);
			}
			die();
		}
		public function saveDocente()
		{
			if(!$_POST){ die(); }
			$id=intval($_POST['idPersona']??0);
			foreach(['txtCi','txtNombre','txtApellido','txtCel','txtEmail'] as $c){ if(empty($_POST[$c])){ echo json_encode(array('status'=>false,'msg'=>'Complete los datos.'),JSON_UNESCAPED_UNICODE);die(); } }
			$d=["ci"=>strClean($_POST['txtCi']),"nombre"=>ucwords(strClean($_POST['txtNombre'])),"apellido"=>ucwords(strClean($_POST['txtApellido'])),"sexo"=>strClean($_POST['listSexo']??'M'),"direccion"=>strClean($_POST['txtDireccion']??''),"cel"=>strClean($_POST['txtCel']),"email"=>strtolower(strClean($_POST['txtEmail'])),"usuario"=>strtolower(strClean($_POST['txtEmail'])),"status"=>intval($_POST['listStatus']??1),"password"=>""];
			$pp=trim($_POST['txtPassword']??''); if($pp!==''){ $d["password"]=password_hash($pp,PASSWORD_DEFAULT); } elseif($id==0){ $d["password"]=\Services\PasswordPolicy::defaultPassword($d["ci"]); }
			if($id>0){ if(!$_SESSION['permisosMod']['u']){ echo json_encode(array('status'=>false,'msg'=>'Sin permiso.'),JSON_UNESCAPED_UNICODE);die(); } $res=$this->model->updateDocente($id,$d); }
			else{ if(!$_SESSION['permisosMod']['w']){ echo json_encode(array('status'=>false,'msg'=>'Sin permiso.'),JSON_UNESCAPED_UNICODE);die(); } $res=$this->model->insertDocente($d); }
			echo json_encode($res=="dato_guardado"?array('status'=>true,'msg'=>'Guardado.'):array('status'=>false,'msg'=>'Error: '.$res),JSON_UNESCAPED_UNICODE);die();
		}
		public function delDocente()
		{
			if($_POST && $_SESSION['permisosMod']['d']){ $id=intval($_POST['idPersona']??0); echo json_encode($this->model->deleteDocente($id)?array('status'=>true,'msg'=>'Dado de baja.'):array('status'=>false,'msg'=>'Error.'),JSON_UNESCAPED_UNICODE); }
			die();
		}
    }
?>
