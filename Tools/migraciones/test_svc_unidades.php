<?php
// TEST servicios Fase 0-4: unidades puras + AuthService/PasswordResetService con stubs.
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';
$fail = 0;
function check($n, $cond) { global $fail; echo ($cond ? 'OK' : 'FALLO')." $n\n"; if (!$cond) { $fail++; } }

// ServiceResult
$r = \Services\ServiceResult::ok('m', ['id' => 5]);
check('result-shape', $r->toArray() === ['status' => true, 'msg' => 'm', 'id' => 5]);
check('result-exists', \Services\ServiceResult::exists('x')->code === 'exist');

// PasswordPolicy
check('policy-login-email', \Services\PasswordPolicy::loginFor('A@x.bo', 'C1') === 'a@x.bo');
check('policy-login-ci', \Services\PasswordPolicy::loginFor('', 'C1') === 'C1');
check('policy-min', \Services\PasswordPolicy::minLength() === 6 && !\Services\PasswordPolicy::meetsLength('12345'));

// GestionService / CursoService
check('gestion-activa', (new \Services\GestionService())->activa() === 2026);
check('gestion-resolver', (new \Services\GestionService())->resolver(0) === 2026);
$o = \Services\CursoService::estadoOcupacion(30, 30);
check('ocup-lleno', $o['label'] === 'Cupo lleno' && $o['class'] === 'full');
$o = \Services\CursoService::estadoOcupacion(26, 30);
check('ocup-warn', $o['label'] === 'Últimos cupos' && $o['libres'] === 4);
check('ocup-neg', \Services\CursoService::cupoLibre(10, 15) === 0);
$n = \Services\CursoService::normalizar(['grado' => 'inicial', 'nivel' => 'X', 'sigla' => 'B', 'turno' => 'T', 'cupo' => -5, 'status' => 9]);
check('norm-inicial', $n === ['nivel' => 'Inicial', 'grado' => 0, 'sigla' => 'B', 'turno' => 'Tarde', 'cupo' => 30, 'status' => 1]);

// CobroService / MateriaService
$c = \Services\CobroService::normalizar(['nombre' => 'Q', 'descripcion' => '', 'tipo' => 'mensual', 'ncuota' => 3, 'valor' => 50, 'status' => 7]);
check('cobro-norm', $c->ok && $c->data === ['nombre' => 'Q', 'descripcion' => '', 'tipo' => 'Mensual', 'ncuota' => 3, 'valor' => 50.0, 'status' => 1]);
check('cobro-invalido', !\Services\CobroService::normalizar(['nombre' => '', 'valor' => 0])->ok);

// TutorService
$t = \Services\TutorService::buildData(['ci' => 'C', 'nombre' => 'N', 'apellido' => 'A', 'sexo' => 'M', 'cel' => '1', 'email' => 'E@X.bo', 'status' => 1, 'password_plain' => '', 'idPadre' => 0, 'parentesco' => 'Padre', 'nacionalidad' => 'Boliviana', 'estado_civil' => '', 'profesion' => '', 'empresa' => '', 'observaciones' => ''], 'D');
check('tutor-default', $t['tutor']['usuario'] === 'e@x.bo' && password_verify('C', $t['tutor']['password']));

// InscripcionService puras
check('req-pend', \Services\InscripcionService::requierePendiente('', '', '', false) === true);
check('req-ok', \Services\InscripcionService::requierePendiente('r', 'e', 'c', false) === false);
$p = \Services\InscripcionService::planDocs('Pendiente_Documentos', null, ['cert_nac' => 1], false, '');
check('plan-auto', $p['estado'] === 'Pendiente_Documentos' && $p['plazo'] !== null && $p['checklist']['cert_nac'] === 1 && $p['compromiso'] === 1);
$p = \Services\InscripcionService::planDocs('Confirmado', '2026-12-01', [], false, '');
check('plan-explicito', $p['plazo'] === '2026-12-01' && $p['compromiso'] === 0);

// FinanzasService / ReciboService / Presenter
$e = \Services\FinanzasService::estadoPension(['estado_pago' => 1]);
check('fin-pagado', $e['clave'] === 'pagado' && strpos($e['badge'], 'Pagado') !== false);
$e = \Services\FinanzasService::estadoPension(['estado_pago' => 0, 'vencida' => 1, 'fecha_vencimiento' => '2026-01-01']);
check('fin-vencido', $e['clave'] === 'vencido');
$e = \Services\FinanzasService::estadoPension(['estado_pago' => 0, 'fecha_vencimiento' => '2026-12-01']);
check('fin-pend', $e['clave'] === 'pendiente');
check('fin-puede', \Services\FinanzasService::puedePagar(['estado_pago' => 0]) && !\Services\FinanzasService::puedePagar(['estado_pago' => 1]));
check('fin-bs', \Services\FinanzasService::bolivianos(25) === 'Bs. 25.00');
check('fin-folio', \Services\FinanzasService::folio(7) === '<b>000007</b>');
check('fin-pct', \Services\FinanzasService::pctCobro(25, 75) === 25.0 && \Services\FinanzasService::pctCobro(0, 0) === 0.0);
check('rec-verif', \Services\ReciboService::esVerificable(['nro_recibo' => 5, 'estado_pago' => 1]) && !\Services\ReciboService::esVerificable(['nro_recibo' => 5, 'estado_pago' => 0]));
check('rec-formato', \Services\ReciboService::normalizarFormato('CARTA') === 'carta' && \Services\ReciboService::normalizarFormato('x') === '');
check('pres-estado', \Services\Presenter::estado(1) === '<span class="badge badge-success">Activo</span>');
check('pres-est', \Services\Presenter::estadoEstudiante(2) === '<span class="badge badge-warning">Inactivo</span>');
check('pres-insc', \Services\Presenter::estadoInscripcion('Confirmado') === '<span class="badge badge-success">Confirmado</span>');

// UserPolicy
check('pol-prot', \Services\UserPolicy::isProtected(1) && !\Services\UserPolicy::isProtected(2));
check('pol-edit', \Services\UserPolicy::puedeEditar(['idUser' => 1, 'userData' => ['idrol' => 1]], ['idrol' => 5, 'id_persona' => 9]));
check('pol-noedit', !\Services\UserPolicy::puedeEditar(['idUser' => 2, 'userData' => ['idrol' => 4]], ['idrol' => 5, 'id_persona' => 9]));
check('pol-nodel-self', !\Services\UserPolicy::puedeEliminar(['idUser' => 1, 'userData' => ['idrol' => 1, 'id_persona' => 1]], ['idrol' => 1, 'id_persona' => 1]));

// AuthService + PasswordResetService con stub de modelo
class FakeLoginModel {
  public $bloqueado = false; public $user = null; public $log = [];
  public function verificarBloqueo($u, $m, $a) { return $this->bloqueado; }
  public function registrarIntentoLogin($u, $e, $ip, $ag = '') { $this->log[] = [$u, $e]; return true; }
  public function loginUser($u, $p) { return ($this->user && $p === 'ok') ? $this->user : false; }
  public function getUserEmail($e) { return $e === 'a@x.bo' ? ['id_persona' => 9, 'nombre' => 'A', 'apellido' => 'B'] : []; }
  public function setTokenUser($id, $t, $m = 60) { $this->log[] = ['token', $t]; return true; }
  public function getUsuario($e, $t) { return $t === 'T' ? ['id_persona' => 9] : []; }
  public function insertPassword($id, $p) { return true; }
}
$authM = new FakeLoginModel(); $authM->user = ['id_persona' => 7, 'status' => 1];
$auth = new \Services\AuthService($authM, 15, 5);
check('auth-ok', $auth->attempt('a', 'ok', 'ip')->ok);
$f = new FakeLoginModel(); $f->user = ['id_persona' => 1, 'status' => 0];
check('auth-inactivo', (new \Services\AuthService($f))->attempt('a', 'ok', 'ip')->msg === 'Usuario inactivo');
$f = new FakeLoginModel(); $f->bloqueado = true;
check('auth-bloq', (new \Services\AuthService($f))->attempt('a', 'x', 'ip')->code === 'bloqueado');
$prs = new \Services\PasswordResetService(new FakeLoginModel(), 60);
check('prs-email-mal', $prs->request('x', fn($d) => true)->code === 'email');
check('prs-nadie', $prs->request('n@x.bo', fn($d) => true)->ok);
$m = new FakeLoginModel();
check('prs-send', (new \Services\PasswordResetService($m))->request('a@x.bo', fn($d) => true)->ok && in_array(['token', 'TOK-IGN'], $m->log, true) === false);
check('prs-fail-invalida', (new \Services\PasswordResetService(new FakeLoginModel()))->request('a@x.bo', fn($d) => false)->code === 'mail');
check('prs-mismatch', (new \Services\PasswordResetService(new FakeLoginModel()))->reset(9, 'a@x.bo', 'T', 'a', 'b')->code === 'mismatch');
check('prs-corta', (new \Services\PasswordResetService(new FakeLoginModel()))->reset(9, 'a@x.bo', 'T', '123', '123')->code === 'corta');
check('prs-ok', (new \Services\PasswordResetService(new FakeLoginModel()))->reset(9, 'a@x.bo', 'T', '123456', '123456')->ok);

echo $fail === 0 ? "SVC OK\n" : "FALLOS: $fail\n";
exit($fail === 0 ? 0 : 1);
