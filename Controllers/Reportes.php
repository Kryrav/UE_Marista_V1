<?php
    class Reportes extends Controllers{
		public function __construct()
		{
			parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){ header('Location: '.base_url().'/login'); }
			getPermisos(13);
		}

		public function index()
		{
			if(empty($_SESSION['permisosMod']['r'])){ header("Location:".base_url().'/dashboard'); }
			$data['page_tag'] = "Reportes de dirección";
			$data['page_title'] = "Reportes <small>Dirección</small>";
			$data['page_name'] = "Reportes";
			$data['page_functions_js'] = "functions_reportes.js";
			$data['gestiones'] = $this->model->gestiones();
			$this->views->getView($this,"reportes",$data);
		}

		private function out($d){ echo json_encode(array('status'=>true,'data'=>$d),JSON_UNESCAPED_UNICODE); die(); }

		public function resumenFinanciero($gestion = 0)
		{ if($_SESSION['permisosMod']['r']){ $this->out($this->model->resumenFinanciero(intval($gestion))); } die(); }
		public function mensual($gestion = 0)
		{ if($_SESSION['permisosMod']['r']){ $this->out($this->model->mensual(intval($gestion))); } die(); }
		public function anual()
		{ if($_SESSION['permisosMod']['r']){ $this->out($this->model->anual()); } die(); }
		public function porCurso($gestion = 0)
		{ if($_SESSION['permisosMod']['r']){ $this->out($this->model->porCurso(intval($gestion))); } die(); }
		public function porCajero($gestion = 0)
		{ if($_SESSION['permisosMod']['r']){ $this->out($this->model->porCajero(intval($gestion))); } die(); }
		public function matriculaStats($gestion = 0)
		{ if($_SESSION['permisosMod']['r']){ $this->out($this->model->matriculaStats(intval($gestion))); } die(); }
		public function estudiantesStats()
		{ if($_SESSION['permisosMod']['r']){ $this->out($this->model->estudiantesStats()); } die(); }
    }
?>
