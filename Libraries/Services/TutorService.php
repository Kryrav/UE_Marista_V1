<?php
namespace Services;

/**
 * Reglas de tutores/apoderados. Saca del controlador el armado de datos,
 * la regla "mismo domicilio" (tenía SQL inline en el controlador) y la
 * clave por defecto (ahora PasswordPolicy).
 */
class TutorService
{
    private $model;

    public function __construct($model = null)
    {
        $this->model = $model ?: new \TutoresModel();
    }

    /**
     * Resuelve el domicilio: si se marcó mismo domicilio se copia la
     * dirección exacta del estudiante desde BD (nunca texto literal).
     */
    public function resolverDomicilio(bool $mismoDom, string $dirTexto, int $idEstudiante): string
    {
        if ($mismoDom || strcasecmp(trim($dirTexto), 'mismo domicilio') == 0) {
            $row = $this->model->select(
                "SELECT p.direccion_dom FROM persona p
                 INNER JOIN estudiante e ON e.id_persona = p.id_persona
                 WHERE e.id_estudiante = ?", [$idEstudiante]);
            return trim($row["direccion_dom"] ?? '');
        }
        return $dirTexto;
    }

    /**
     * Arma los datos del tutor desde el POST ya saneado (misma forma que antes).
     * @return array{tutor:array, padre:array}
     */
    public static function buildData(array $p, string $dirTutor): array
    {
        $email = strtolower(trim($p['email'] ?? ''));
        $tutor = [
            "ci" => $p['ci'],
            "nombre" => $p['nombre'],
            "apellido" => $p['apellido'],
            "sexo" => $p['sexo'] ?? 'M',
            "direccion" => $dirTutor,
            "cel" => $p['cel'],
            "email" => $email,
            "usuario" => $email,
            "status" => $p['status'] ?? 1,
            "password" => "",
        ];
        $pwdPlain = trim($p['password_plain'] ?? '');
        if ($pwdPlain !== '') {
            $tutor["password"] = password_hash($pwdPlain, PASSWORD_DEFAULT);
        } elseif (($p['idPadre'] ?? 0) == 0) {
            $tutor["password"] = PasswordPolicy::defaultPassword($p['ci']);
        }
        $padre = [
            "parentesco" => $p['parentesco'] ?? 'Padre',
            "nacionalidad" => $p['nacionalidad'] ?? 'Boliviana',
            "estado_civil" => $p['estado_civil'] ?? '',
            "profesion" => $p['profesion'] ?? '',
            "empresa" => $p['empresa'] ?? '',
            "observaciones" => $p['observaciones'] ?? '',
        ];
        return ["tutor" => $tutor, "padre" => $padre];
    }

    /**
     * Crea o actualiza el vínculo. Interpreta los códigos del modelo
     * ('dato_guardado', 'exist:...', 'exist', otro) a ServiceResult.
     */
    public function guardar(int $idPadre, array $tutor, int $idEstudiante, array $padre): ServiceResult
    {
        if ($idPadre > 0) {
            $res = $this->model->updateTutor($idPadre, $tutor, $idEstudiante, $padre);
            $msg = 'Tutor actualizado.';
        } else {
            $res = $this->model->insertTutor($tutor, $idEstudiante, $padre);
            $msg = 'Tutor registrado.';
        }
        if ($res == "dato_guardado") {
            return ServiceResult::ok($msg);
        }
        if (strpos((string)$res, 'exist:') === 0) {
            return ServiceResult::exists(substr((string)$res, 6));
        }
        if ($res == "exist") {
            return ServiceResult::exists('CI o email ya registrado.');
        }
        return ServiceResult::fail('No se pudo guardar: ' . $res);
    }
}
