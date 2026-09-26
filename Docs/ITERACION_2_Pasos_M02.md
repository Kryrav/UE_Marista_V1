# ITERACIÓN 2 — Rematriculación + estados terminales (M02)

**Fecha:** 2026-09-26 · **BD:** `db_marista` (MySQL 9.1.0)
**Criterios:** F-04 (flujo diferenciado de renovación), F-09 (retirado/trasladado/egresado)
**Riesgo:** bajo-medio (ADD NULL + SPs con handler; pensiones intactas)

## 1. Qué se hizo

- **Rematricular en 1 clic:** botón `⏩` en cada fila de `Matrícula` + botón en la ficha del estudiante (`Ficha > Matrículas > Rematricular`). Precarga CI/curso anterior, propone gestión activa (o última+1), solo pide nuevo paralelo + tipo. Reutiliza `insertMatricula()` → genera las 10 pensiones; si ya existe en destino avisa `Ya está matriculado` (sin duplicar).
- **Endpoints nuevos:** `Matricula::ultimaMatricula?ci=` (JSON última matrícula + gestión activa) y `Matricula::rematricular` (POST ci/gestion/paralelo/tipo, permiso `w`).
- **Estados terminales:** `Retirado / Trasladado / Egresado` seleccionables en el modal, con **motivo obligatorio** (`motivo_estado`) y badges propios (gris/oscuro/azul). El historial se preserva: no se borra la fila, no se tocan pensiones. Ver motivo en tabla (tooltip), `fntViewMatricula` y ficha.
- **Regla de baja intacta:** `delEstudiante` sigue bloqueado si hay matrículas con `status=1` (las terminales cuentan como historial).

## 2. Hallazgo crítico corregido (transacción huérfana)

Síntoma en pruebas: un `UPDATE` vía modelo era visible en su propia conexión pero invisible para otras, y los `DELETE` se colgaban con `1205 Lock wait timeout`, con `INNODB_TRX` vacío.
Causa: `matricular_estudiante_y_generar_pensiones` (y los SPs escritores) hacen `START TRANSACTION` y ante un `SIGNAL` (duplicado/validación) abortaban **sin ROLLBACK**: la transacción quedaba abierta con locks compartidos; los writes siguientes del request quedaban sin commit y se perdían al cerrar. En producción el impacto era limitado (1 write por request), pero era una mina.
Corrección en dos capas (sin cambio en ruta feliz):
  a) `EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;` en `sp_registrar`, `updateEstudiante` y `matricular_...` (cuerpo vivo preservado).
  b) `Libraries/Core/Mysql.php`: `rollbackLeaked()` — ROLLBACK best-effort en cada `catch` si el llamador no gestiona transacción propia (contador `txnDepth`; `deleteEstudiante` intacto). Nota: `PDO::inTransaction()` no detecta txns del servidor, por eso es incondicional (no-op si no hay txn).

## 3. Archivos tocados

| Archivo | Cambio |
|---|---|
| `Tools/migraciones/m02_rematriculacion_estados.sql` | `motivo_estado` + `listar_matriculas` + 3 SPs con handler |
| `Tools/migraciones/test_m02_rematricula.php` | TEST M02 OK (alta, remat, dup-guard, terminal, ficha, limpieza) |
| `Libraries/Core/Mysql.php` | `txnDepth` + `rollbackLeaked()` en 5 catch + `rollback()` tolerante |
| `Models/MatriculaModel.php` | `ultimaMatriculaByCi()`, `selectMatricula()` con motivo, `updateMatricula()` con motivo |
| `Models/EstudiantesModel.php` | `getFicha()` expone `motivo_estado` |
| `Controllers/Matricula.php` | `ultimaMatricula`, `rematricular`, motivo obligatorio en terminales, badges + botón ⏩ |
| `Helpers/Helpers.php` | `estadosInscripcion(), esEstadoTerminal(), esEstadoInscripcionValido()` |
| `Views/Template/Modals/modalMatricula.php` | 3 estados + campo motivo |
| `Assets/js/functions_matricula.js` | modo rematricular, `fntRematricular()`, `toggleMotivoBox()`, precarga motivo |
| `Assets/js/functions_estudiantes.js` | motivo en ficha + botón Rematricular → Matrícula (sessionStorage) |

## 4. Pasos

- [x] Backup PRE-M02 (759 KB) + POST-M02 (761 KB) en `Backups/M02_2026-09-26_Rematriculacion-Estados/`.
- [x] Migración aplicada (columna + 4 SPs, EXIT 0). Verificado `motivo_estado YES`.
- [x] `php -l` + `node --check` OK. TEST M02 OK sin restos (`left TEST=0`).
- [ ] Prueba Secretaría en UI (ver Manual v1.2 §4 y §7).
