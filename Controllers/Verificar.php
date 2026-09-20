<?php
    // Controlador PÚBLICO (sin login): verificación de recibos vía QR.
    class Verificar extends Controllers{
		public function __construct()
		{
			parent::__construct();
			// Sin session_start ni getPermisos: acceso público.
		}

		public function index()
		{
			$data['page_tag'] = "Verificar recibo - Marista";
			require_once("Views/Verificar/index.php");
			die();
		}

		// verificar/recibo/{nro}/{firma12}
		public function recibo($nro = 0, $firma = '')
		{
			$nro = intval($nro);
			$firma = preg_replace('/[^a-f0-9]/', '', strtolower((string)$firma));
			$ok = ($nro > 0 && $firma !== '' && hash_equals(firmUrl($nro), $firma));
			$data['page_tag'] = "Verificar recibo N° $nro - Marista";
			$data['nro'] = $nro;
			$data['valido'] = false;
			$data['recibo'] = null;
			if($ok){
				require_once("Models/PensionesModel.php");
				$mp = new PensionesModel();
				$row = $mp->verificarRecibo($nro);
				if(!empty($row) && intval($row["estado_pago"]) === 1){
					$data['valido'] = true;
					$data['recibo'] = $row;
				}
			}
			require_once("Views/Verificar/recibo.php");
			die();
		}
		// verificar/estudiante/{id}/{firma12} — QR del carnet
		public function estudiante($id = 0, $firma = '')
		{
			$id = intval($id);
			$firma = preg_replace('/[^a-f0-9]/', '', strtolower((string)$firma));
			$esperada = substr(hash('sha256', 'est|' . $id . '|' . QR_SECRET), 0, 12);
			$data['page_tag'] = "Verificar estudiante - Marista";
			$data['valido'] = false;
			$data['est'] = null;
			if($id > 0 && $firma !== '' && hash_equals($esperada, $firma)){
				require_once("Models/EstudiantesModel.php");
				require_once("Libraries/Core/BaseModel.php");
				require_once("Libraries/Core/Persona.php");
				$me = new EstudiantesModel();
				$ficha = $me->getFicha($id);
				if(!empty($ficha)){
					$mat = null;
					foreach(($ficha["matriculas"] ?: []) as $mm){
						if($mat === null || intval($mm["gestion"]) > intval($mat["gestion"])){ $mat = $mm; }
					}
					$data['valido'] = true;
					$data['est'] = [
						"nombre" => $ficha["estudiante"]["nombre"] . ' ' . $ficha["estudiante"]["apellido"],
						"ci" => $ficha["estudiante"]["ci"],
						"rude" => $ficha["estudiante"]["rude"],
						"folio" => $ficha["estudiante"]["folio_fisico"] ?? null,
						"estado" => intval($ficha["estudiante"]["estudiante_status"]),
						"matricula" => $mat
					];
				}
			}
			require_once("Views/Verificar/estudiante.php");
			die();
		}
    }
?>
