<?php
    class Administrativos extends Controllers{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){ header('Location: '.base_url().'/login'); }
			getPermisos(9);
		}
		public function index()
		{
			if(empty($_SESSION['permisosMod']['r'])){ header("Location:".base_url().'/dashboard'); }
			$data['page_tag']="Administrativos"; $data['page_title']="Administrativos <small>Personal</small>"; $data['page_name']="Administrativos"; $data['page_functions_js']="functions_administrativos.js";
			$this->views->getView($this,"administrativos",$data);
		}
		public function getAdmins()
		{
			if($_SESSION['permisosMod']['r']){
				$a=$this->model->selectAdmins();
				foreach($a as $k=>$v){
					$a[$k]['status']=\Services\Presenter::estado(intval($v['status'] ?? 0));
					$bv='<button class="btn btn-info btn-sm" onClick="fntViewAdmin('.$v['id_persona'].')"><i class="far fa-eye"></i></button>';
					$be='<button class="btn btn-primary btn-sm" onClick="fntEditAdmin('.$v['id_persona'].')"><i class="fas fa-pencil-alt"></i></button>';
					$bd='<button class="btn btn-danger btn-sm" onClick="fntDelAdmin('.$v['id_persona'].')"><i class="far fa-trash-alt"></i></button>';
					$a[$k]['options']='<div class="text-center">'.$bv.' '.$be.' '.$bd.'</div>';
				}
				echo json_encode($a,JSON_UNESCAPED_UNICODE);
			}
			die();
		}
		public function getAdmin(int $id){ if($_SESSION['permisosMod']['r']&&$id>0){ $d=$this->model->selectAdmin($id); echo json_encode(empty($d)?array('status'=>false,'msg'=>'No encontrado'):array('status'=>true,'data'=>$d),JSON_UNESCAPED_UNICODE);} die(); }
		public function roles(){ if($_SESSION['permisosMod']['r']){ $r=$this->model->roles(); $h=''; foreach($r as $x){ $h.='<option value="'.$x['idrol'].'">'.$x['nombrerol'].'</option>'; } echo $h; } die(); }
		public function saveAdmin()
		{
			if(!$_POST){ die(); }
			$id=intval($_POST['idPersona']??0);
			foreach(['txtCi','txtNombre','txtApellido','txtCel','txtEmail','listRol'] as $c){ if(empty($_POST[$c])){ echo json_encode(array('status'=>false,'msg'=>'Complete los datos.'),JSON_UNESCAPED_UNICODE);die(); } }
			$d=["ci"=>strClean($_POST['txtCi']),"nombre"=>ucwords(strClean($_POST['txtNombre'])),"apellido"=>ucwords(strClean($_POST['txtApellido'])),"sexo"=>strClean($_POST['listSexo']??'M'),"direccion"=>strClean($_POST['txtDireccion']??''),"cel"=>strClean($_POST['txtCel']),"email"=>strtolower(strClean($_POST['txtEmail'])),"usuario"=>strtolower(strClean($_POST['txtEmail'])),"id_rol"=>intval($_POST['listRol']),"status"=>intval($_POST['listStatus']??1),"password"=>""];
			$pp=trim($_POST['txtPassword']??''); if($pp!==''){ $d["password"]=password_hash($pp,PASSWORD_DEFAULT);} elseif($id==0){ $d["password"]=\Services\PasswordPolicy::defaultPassword($d["ci"]); }
			if($id>0){ $res=$this->model->updateAdmin($id,$d); } else { $res=$this->model->insertAdmin($d); }
			echo json_encode($res=="dato_guardado"?array('status'=>true,'msg'=>'Guardado.'):array('status'=>false,'msg'=>'Error: '.$res),JSON_UNESCAPED_UNICODE);die();
		}
		public function delAdmin(){ if($_POST&&$_SESSION['permisosMod']['d']){ $id=intval($_POST['idPersona']??0); echo json_encode($this->model->deleteAdmin($id)?array('status'=>true,'msg'=>'Dado de baja.'):array('status'=>false,'msg'=>'Error o protegido.'),JSON_UNESCAPED_UNICODE);} die(); }
    }
?>
