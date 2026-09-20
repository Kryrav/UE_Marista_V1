<?php 
	class GestionModel extends Mysql
	{
		private $intIdGestion;
		private $dateInicio;
		private $dateFin;
		private $strGestion;
		private $intPension;
		private $strDescripcion;
		private $intStatus;

	
		public function __construct()
		{
			parent::__construct();
		}	
		
		// Método para listar las gesiones pasadas
		public function getGestiones()
		{
			try {
				$sql="call obtenerGestiones()";
				$request=$this->select_all($sql);
				// Veridficamos si llegó consulta
				if ($request) {
					return $request;
				}else {
					return "Error en la consulta!.";
				}
			} catch (PDOException $e) {
                // Capturamos errores específicos del procedimiento almacenado
                    // Manejo de otro tipo de error
                    return "DB-ERROR: " . $e->getMessage();
                
            }
            die();

		}
		public function selectGestionAct()
		{
			try {
				$sql="CALL get_gestion_activa()";
				$request	=	$this->select($sql);
				if ($request) {
					return $request;
				}else {
					return "";
				}
			} catch (PDOException $e) {
				// Capturamos errores específicos del procedimiento almacenado
                    // Manejo de otro tipo de error
                    return "DB-ERROR: " . $e->getMessage();
			}
			die();

		}

		public function selectGestion(int $gestion)
		{
			$this->intIdGestion	=	$gestion;
			try {
				$sql="CALL obtenerGestion($this->intIdGestion )";
				$request	=	$this->select($sql);
				if ($request) {
					return $request;
				}else {
					return "";
				}
			} catch (PDOException $e) {
				// Capturamos errores específicos del procedimiento almacenado
                    // Manejo de otro tipo de error
                    return "DB-ERROR: " . $e->getMessage();
			}
			die();
		}

		public function deleteGestion()
		{
			try {
				$sql="call sp_close_active_gestion()";
				$request=$this->select($sql);
				// Veridficamos si llegó consulta
				if ($request) {
					return $request;
				}else {
					return "Error en la consulta!.";
				}
			} catch (PDOException $e) {
                // Capturamos errores específicos del procedimiento almacenado
                    // Manejo de otro tipo de error
                    return "DB-ERROR: " . $e->getMessage();
                
            }
            die();
		}

		public function insertNewGestion(int $intGestion,string $dateInicio,string $dateFin,string $strGest,int $intPension,string $strDescripcion,int $status)
		{
			$this->intIdGestion = $intGestion;
			$this->dateInicio = $dateInicio;
			$this->dateFin = $dateFin;
			$this->strGestion = $strGest;
			$this->intPension = $intPension;
			$this->strDescripcion = $strDescripcion;
			$this->intStatus = $status;
			

			try {
				$sql = "CALL insertGestion(?,?,?,?,?,?,?)";
				$arrData = array($this->intIdGestion,
								$this->dateInicio,
								$this->dateFin,
								$this->strGestion,
								$this->intPension,
								$this->strDescripcion,
								$this->intStatus);
				$request = $this->insert($sql,$arrData);
				// Veridficamos si llegó consulta
				if ($request) {
					return "dato_guardado";
				}else {
					return "Error en la consulta!.";
				}
			} catch (PDOException $e) {
                // Capturamos errores específicos del procedimiento almacenado
                    // Manejo de otro tipo de error
                    return "DB-ERROR: " . $e->getMessage();
                
            }
            die();

		}
		
		public function updateGestion(int $intGestion,string $dateInicio,string $dateFin,string $strGest,int $intPension,string $strDescripcion)
			{
				$this->intIdGestion = $intGestion;
				$this->dateInicio = $dateInicio;
				$this->dateFin = $dateFin;
				$this->strGestion = $strGest;
				$this->intPension = $intPension;
				$this->strDescripcion = $strDescripcion;
				

				try {
					$sql = "CALL updateGestion(?,?,?,?,?,?)";
					$arrData = array($this->intIdGestion,
									$this->dateInicio,
									$this->dateFin,
									$this->strGestion,
									$this->intPension,
									$this->strDescripcion);
					$request = $this->update($sql,$arrData);
					// Verificamos si llegó consulta
					if ($request) {
						return "dato_guardado";
					}else {
						return "Error en la consulta!.";
					}
				} catch (PDOException $e) {
					// Capturamos errores específicos del procedimiento almacenado
						// Manejo de otro tipo de error
						return "DB-ERROR: " . $e->getMessage();
					
				}
				die();
			
			}
		}
 ?>