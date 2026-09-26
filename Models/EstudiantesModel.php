<?php
  class EstudiantesModel extends persona
  {
    public function __construct()
    {
      parent::__construct();
    }

    public function selectEstudiantes()
    {
      $sql = "CALL ListarEstudiantesRegistrados()";
      return $this->select_all($sql);
    }

    public function selectEstudiante(int $IdEst)
    {
      $sql = "CALL ListarInformacionEstudiante(?)";
      return $this->select($sql, [$IdEst]);
    }

    // Ficha 360°: datos + tutores + matrículas + resumen de pensiones
    public function getFicha(int $IdEst)
    {
      $est = $this->selectEstudiante($IdEst);
      if(empty($est)){ return null; }
      $tutores = $this->select_all(
        "SELECT pa.id_padre, pa.tipo_parentesco, t.nombre, t.apellido, t.cel, t.email
         FROM padre pa INNER JOIN persona t ON pa.id_persona = t.id_persona
         WHERE pa.id_estudiante = ? AND pa.status != 0", [$IdEst]);
      $matriculas = $this->select_all(
        "SELECT m.id_matricula, m.gestion, m.tipo, m.estado_inscripcion, m.status,
                CONCAT(pa.nivel,' ',pa.grado,' \"',pa.sigla,'\"') AS curso
         FROM matricula m LEFT JOIN paralelo pa ON pa.id_paralelo = m.id_paralelo
         WHERE m.id_estudiante = ? ORDER BY m.gestion DESC", [$IdEst]);
      $pens = $this->select(
        "SELECT COUNT(*) AS total, SUM(estado_pago=1) AS pagadas, SUM(estado_pago=0) AS pendientes,
                COALESCE(SUM(CASE WHEN estado_pago=1 THEN monto ELSE 0 END),0) AS cobrado,
                COALESCE(SUM(CASE WHEN estado_pago=0 THEN monto ELSE 0 END),0) AS deuda
         FROM pensiones p INNER JOIN matricula m ON p.id_matricula = m.id_matricula
         WHERE m.id_estudiante = ? AND p.status = 1", [$IdEst]);
      // Detalle ordenado: gestión reciente primero, mes en orden calendario
      // (mes es texto: Febrero..Noviembre, se ordena con FIELD, no alfabético).
      $ordenMes = "FIELD(p.mes,'Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre')";
      $pensDet = $this->select_all(
        "SELECT p.id_pensiones, m.id_matricula, m.gestion, p.mes, p.monto, p.estado_pago, p.tipo_pago, p.fecha_reg_pago,
                p.fecha_vencimiento, (p.estado_pago = 0 AND p.fecha_vencimiento < CURDATE()) AS vencida,
                CONCAT(pa.nivel,' ',pa.grado,' \"',pa.sigla,'\"') AS curso
         FROM pensiones p
         INNER JOIN matricula m ON p.id_matricula = m.id_matricula
         LEFT JOIN paralelo pa ON pa.id_paralelo = m.id_paralelo
         WHERE m.id_estudiante = ? AND p.status = 1
         ORDER BY m.gestion DESC, $ordenMes", [$IdEst]);
      return ["estudiante"=>$est, "tutores"=>$tutores ?: [], "matriculas"=>$matriculas ?: [],
              "pensiones"=>$pens ?: ["total"=>0,"pagadas"=>0,"pendientes"=>0,"cobrado"=>0,"deuda"=>0],
              "pensiones_detalle"=>$pensDet ?: []];
    }

    // Pre-chequeo de duplicados con mensaje específico. $excludeIdEstudiante=null en insert.
    // ITERACIÓN 1: cel/email/rude son DIFERIBLES — solo se validan si traen valor.
    public function checkDuplicados(string $ci, string $cel, string $email, string $rude, $excludeIdEstudiante = null, $folio = null)
    {
      $excPersona = null;
      if($excludeIdEstudiante){
        $row = $this->select("SELECT id_persona FROM estudiante WHERE id_estudiante = ?", [$excludeIdEstudiante]);
        $excPersona = $row["id_persona"] ?? null;
      }
      $q = "SELECT id_persona FROM persona WHERE ci = ?";
      $p = [$ci];
      if($excPersona){ $q .= " AND id_persona != ?"; $p[] = $excPersona; }
      if($this->select($q, $p)){ return "El CI ya está registrado en otro estudiante."; }
      $cel = trim($cel ?? '');
      if($cel !== ''){
        $q = "SELECT id_persona FROM persona WHERE cel = ?";
        $p = [$cel];
        if($excPersona){ $q .= " AND id_persona != ?"; $p[] = $excPersona; }
        if($this->select($q, $p)){ return "El celular ya está registrado en otro usuario."; }
      }
      $email = trim(strtolower($email ?? ''));
      if($email !== ''){
        $q = "SELECT id_persona FROM persona WHERE email = ?";
        $p = [$email];
        if($excPersona){ $q .= " AND id_persona != ?"; $p[] = $excPersona; }
        if($this->select($q, $p)){ return "El email ya está registrado en otro usuario."; }
      }
      $rude = trim($rude ?? '');
      if($rude !== ''){
        $q = "SELECT id_estudiante FROM estudiante WHERE rude = ?";
        $p = [$rude];
        if($excludeIdEstudiante){ $q .= " AND id_estudiante != ?"; $p[] = $excludeIdEstudiante; }
        if($this->select($q, $p)){ return "El RUDE ya está registrado en otro estudiante."; }
      }
      if($folio !== null && $folio !== ""){
        $q = "SELECT id_estudiante FROM estudiante WHERE folio_fisico = ?";
        $p = [(int)$folio];
        if($excludeIdEstudiante){ $q .= " AND id_estudiante != ?"; $p[] = $excludeIdEstudiante; }
        if($this->select($q, $p)){ return "El folio N° $folio ya está asignado a otro estudiante."; }
      }
      return null;
    }

    // Siguiente folio disponible (MAX+1). El UNIQUE uq_folio_fisico es la guarda final.
    public function nextFolio()
    {
      $row = $this->select("SELECT COALESCE(MAX(folio_fisico),0)+1 AS nx FROM estudiante");
      return (int)($row["nx"] ?? 1);
    }

    // DOCTRINA DE ESTADOS (separación de responsabilidades):
    // - estudiante.status (1=Activo, 2=Inactivo, 0=Eliminado) gobierna la VIDA ACADÉMICA:
    //   solo status=1 puede matricularse y aparecer en selectores/listas académicas.
    //   NO restringe el acceso al sistema.
    // - persona.status (1=con acceso, 0=bloqueado) gobierna el ACCESO y se gestiona
    //   ÚNICAMENTE desde el módulo Usuarios (más alta/baja aquí). Los SP no lo tocan
    //   en update; en insert el SP replica el valor y aquí se corrige a 1.
    private function grantPersonaAccess(string $ci)
    {
      $this->update("UPDATE persona SET status = 1 WHERE ci = ?", [$ci]);
    }

    public function insertEstudiante(string $strCi, string $strRUDE, string $strlistEst, string $strNombre, string $strApellido, string $strSex, string $strTelefono, string $strEmail, string $strDireccion, string $dateFNacimiento, string $strPais, string $strCiudad, string $strProvincia, string $strColegioProc, string $strEmergencia, string $intTipoId, string $strPassword, int $intStatus, string $strFoto = null, $folio = null, string $estante = null, string $gaveta = null, string $estadoLegajo = null)
    {
      // ITERACIÓN 1: diferibles "" -> NULL; usuario fallback = email o CI
      $strTelefono = trim($strTelefono ?? ''); if($strTelefono === ''){ $strTelefono = null; }
      $strEmail = trim(strtolower($strEmail ?? '')); if($strEmail === ''){ $strEmail = null; }
      $strRUDE = trim($strRUDE ?? ''); if($strRUDE === ''){ $strRUDE = null; }
      $strDireccion = trim($strDireccion ?? ''); if($strDireccion === ''){ $strDireccion = null; }
      $strColegioProc = trim($strColegioProc ?? ''); if($strColegioProc === ''){ $strColegioProc = null; }
      $strProvincia = trim($strProvincia ?? ''); if($strProvincia === ''){ $strProvincia = null; }
      $strCiudad = trim($strCiudad ?? ''); if($strCiudad === ''){ $strCiudad = null; }
      if(trim($strPais ?? '') === ''){ $strPais = 'Bolivia'; }
      $strEmergencia = trim($strEmergencia ?? ''); if($strEmergencia === ''){ $strEmergencia = null; }
      $strUsuario = ($strEmail !== null && $strEmail !== '') ? $strEmail : $strCi;
      if($folio === null || $folio === ""){ $folio = $this->nextFolio(); $auto = true; } else { $folio = (int)$folio; $auto = false; }
      $dup = $this->checkDuplicados($strCi, (string)($strTelefono ?? ''), (string)($strEmail ?? ''), (string)($strRUDE ?? ''), null, $folio);
      if($dup){ return "exist:".$dup; }
      // Reintentos ante carrera por el folio (UNIQUE como guarda final)
      for($try = 0; $try < 3; $try++){
        try {
            $sql = "CALL sp_registrar_estudiante(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $arrData = array($strCi, $strNombre, $strApellido, $strSex, $strDireccion, $strTelefono,
                $strEmail, $strUsuario, $strPassword, $intTipoId, $strColegioProc, $strRUDE,
                $strProvincia, $strCiudad, $strPais, $dateFNacimiento, $strEmergencia,
                $strlistEst, $intStatus, $strFoto, $folio, $estante, $gaveta, $estadoLegajo);
            $request_insert = $this->insert($sql, $arrData);
            if ($request_insert) {
              // Alta siempre con acceso: el estado académico NO restringe el sistema.
              $this->grantPersonaAccess($strCi);
              return "dato_guardado";
            }
            return "Error al guardar";
        } catch (Exception $e) {
            $msg = $e->getMessage();
            if($auto && (strpos($msg, 'uq_folio_fisico') !== false || strpos($msg, 'folio ya está asignado') !== false)){
              $folio = $this->nextFolio();
              continue;
            }
            if(strpos($msg, 'folio ya está asignado') !== false){ return "exist:El folio N° $folio ya está asignado a otro estudiante."; }
            return "Error al insertar estudiante: " . $msg;
        }
      }
      return "Error al guardar: no se pudo asignar folio.";
    }

    public function updateEstudiante(int $idStudent, string $strCi, string $strRUDE, string $strlistEst, string $strNombre, string $strApellido, string $strSex, string $strTelefono, string $strEmail, string $strDireccion, string $dateFNacimiento, string $strPais, string $strCiudad, string $strProvincia, string $strColegioProc, string $strEmergencia, string $intTipoId, string $strPassword, int $intStatus, string $strFoto = null, $folio = null, string $estante = null, string $gaveta = null, string $estadoLegajo = null)
    {
      // ITERACIÓN 1: diferibles "" -> NULL (conservar si no se envían en edición parcial)
      $norm = function($v){ $v = trim((string)($v ?? '')); return $v === '' ? null : $v; };
      // No forzar NULL aquí para no pisar: checkDuplicados ya ignora vacíos
      $dup = $this->checkDuplicados($strCi, $strTelefono, $strEmail, $strRUDE, $idStudent, $folio);
      if($dup){ return "exist:".$dup; }
      // Si no se digitó clave nueva, conservar el hash actual (no resetear acceso)
      if($strPassword === "" || $strPassword === null){
        $row = $this->select(
          "SELECT p.password FROM persona p INNER JOIN estudiante e ON e.id_persona = p.id_persona WHERE e.id_estudiante = ?",
          [$idStudent]);
        $strPassword = $row["password"] ?? "";
      }
      // Si no se envió foto nueva, conservar la actual
      if($strFoto === null){
        $row = $this->select("SELECT foto FROM estudiante WHERE id_estudiante = ?", [$idStudent]);
        $strFoto = $row["foto"] ?? null;
      }
      // Si no se envió folio, conservar el actual (no reasignar)
      if($folio === null || $folio === ""){
        $row = $this->select("SELECT folio_fisico, estante, gaveta, estado_legajo FROM estudiante WHERE id_estudiante = ?", [$idStudent]);
        if($folio === null || $folio === ""){ $folio = $row["folio_fisico"] ?? null; }
        if($estante === null){ $estante = $row["estante"] ?? null; }
        if($gaveta === null){ $gaveta = $row["gaveta"] ?? null; }
        if($estadoLegajo === null){ $estadoLegajo = $row["estado_legajo"] ?? null; }
      } else { $folio = (int)$folio; }
      try {
          // ITERACIÓN 1: usuario fallback = email o CI (no dejar "" que rompe login)
          $strUsuarioUpd = trim((string)$strEmail);
          if($strUsuarioUpd === ''){ $strUsuarioUpd = $strCi; }
          $sql = "CALL updateEstudiante(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
          $arrData = array($idStudent, $strCi, $strRUDE, $strlistEst, $strNombre, $strApellido,
              $strSex, $strTelefono, $strEmail, $strDireccion, $dateFNacimiento, $strPais,
              $strCiudad, $strProvincia, $strColegioProc, $strEmergencia, $intTipoId,
              $strPassword, $strUsuarioUpd, $intStatus, $strFoto, $folio, $estante, $gaveta, $estadoLegajo);
          $request = $this->update($sql, $arrData);
          if ($request) {
            // Intencionalmente NO se toca persona.status: el estado académico
            // no restringe el acceso (se gestiona en Usuarios).
            return "dato_guardado";
          }
          return "Error al guardar";
      } catch (Exception $e) {
          return "Error al actualizar estudiante: " . $e->getMessage();
      }
    }

    // Historial de pagos de UNA matrícula (hoja imprimible): cabecera + pensiones + colegio
    public function getHistorialMatricula(int $idMatricula)
    {
      $cab = $this->select(
        "SELECT m.id_matricula, m.gestion, m.tipo, m.estado_inscripcion, m.folio, m.fecha_reg AS fecha_matricula,
                e.id_estudiante, e.rude, e.folio_fisico,
                p.ci, p.nombre, p.apellido, p.cel, p.email,
                CONCAT(pa.nivel,' ',pa.grado,' \"',pa.sigla,'\"') AS curso, pa.turno
         FROM matricula m
         INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
         INNER JOIN persona p ON e.id_persona = p.id_persona
         LEFT JOIN paralelo pa ON pa.id_paralelo = m.id_paralelo
         WHERE m.id_matricula = ?", [$idMatricula]);
      if(empty($cab)){ return null; }
      $ordenMes = "FIELD(mes,'Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre')";
      $pens = $this->select_all(
        "SELECT mes, monto, estado_pago, tipo_pago, codigo, nombre AS pagador, fecha_reg_pago
         FROM pensiones WHERE id_matricula = ? AND status = 1 ORDER BY $ordenMes", [$idMatricula]);
      $col = $this->select("SELECT * FROM colegio WHERE id_colegio = 1");
      $tot = ["cuotas"=>0,"pagadas"=>0,"pendientes"=>0,"cobrado"=>0,"deuda"=>0];
      foreach(($pens ?: []) as $r){
        $tot["cuotas"]++;
        if($r["estado_pago"]==1){ $tot["pagadas"]++; $tot["cobrado"] += (float)$r["monto"]; }
        else{ $tot["pendientes"]++; $tot["deuda"] += (float)$r["monto"]; }
      }
      return ["cabecera"=>$cab, "pensiones"=>$pens ?: [], "colegio"=>$col, "totales"=>$tot];
    }

    // Baja segura: bloquea si tiene matrícula activa; si no, da de baja
    // estudiante + persona (bloquea acceso) + vínculos de tutor.
    public function deleteEstudiante(int $IdEst)
    {
      $mat = $this->select(
        "SELECT COUNT(*) AS c FROM matricula WHERE id_estudiante = ? AND status = 1",
        [$IdEst]);
      if(!empty($mat["c"])){
        return "has_matricula";
      }
      try {
        $this->beginTransaction();
        $this->update("UPDATE padre SET status = 0 WHERE id_estudiante = ?", [$IdEst]);
        $this->update("UPDATE estudiante SET status = 0 WHERE id_estudiante = ?", [$IdEst]);
        $this->update("UPDATE persona SET status = 0 WHERE id_persona = (SELECT id_persona FROM (SELECT id_persona FROM estudiante WHERE id_estudiante = ?) t)", [$IdEst]);
        $this->commit();
        return true;
      } catch (Exception $e) {
        try{ $this->rollback(); }catch(Exception $x){}
        return false;
      }
    }
  }
?>
