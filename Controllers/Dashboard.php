<?php 

	class Dashboard extends Controllers{
		public function __construct()
		{
			parent::__construct();
			session_start();
			session_regenerate_id(true);
			if(empty($_SESSION['login']))
			{
				header('Location: '.base_url().'/login');
			}
			getPermisos(1);
		}

		public function index()
		{
			$data['page_id'] = 2;
			$data['page_tag'] = "Dashboard - Marista";
			$data['page_title'] = "Dashboard <small>Resumen del sistema</small>";
			$data['page_name'] = "dashboard";
			$data['page_functions_js'] = "functions_dashboard.js";
			try{ $data['stats'] = $this->model ? $this->model->stats() : []; }
			catch(Exception $e){ $data['stats'] = []; }
			$this->views->getView($this,"dashboard",$data);
		}

		public function stats()
		{
			echo json_encode($this->model->stats(), JSON_UNESCAPED_UNICODE);
			die();
		}

	}
 ?>
