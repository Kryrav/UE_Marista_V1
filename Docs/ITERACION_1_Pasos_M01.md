# ITERACIÓN 1 — Documentación diferida 30 días hábiles (M01)

**Fecha:** 2026-09-26 · **BD:** `db_marista` (MySQL 9.1.0, 202 estudiantes, 332 matrículas)
**Norma:** Inscripción durante todo el año, sin bloqueo por falta de documentos; compromiso a 30 días hábiles.
**Criterios:** N-01, N-07, F-06, F-01, U-04 · **Riesgo:** bajo (solo ADD NULL, sin borrar datos)

## 1. Qué se está realizando y por qué

Antes el sistema exigía `RUDE + email + celular` obligatorios y no tenía estado de plazo.
Eso bloqueaba inscripciones legales. Ahora:

- `RUDE / email / celular` pasan a **diferibles**: se puede inscribir solo con `CI + nombres + apellidos + fecha nacimiento + apoderado`.
- Nueva marca `matricula.estado_inscripcion = Pendiente_Documentos` + `plazo_documentos_hasta` (30 días hábiles lun–vie) + `docs_checklist {ci, cert_nac, rude, solicitud}` + `compromiso_firmado` + `docs_observacion`.
- Si en el alta faltan diferibles, el sistema marca pendiente **automáticamente** aunque no tilden el check.
- SPs `sp_registrar_estudiante (24p)` y `updateEstudiante (25p)` ahora permiten `NULL` en diferibles y generan `usuario = email o CI`.
- Lista de matrículas muestra badge ámbar `Pendiente docs (Nd)` / `vencido`.

## 2. Archivos tocados (código ya en tu carpeta, sin aplicar BD aún hasta paso 5)

| Archivo | Cambio |
|---|---|
| `Tools/migraciones/m01_documentacion_pendiente.sql` | Migración M01 (la que se aplica a BD) |
| `Helpers/Helpers.php` | `plazo30Habiles(), diasHabilesRestantes(), docsChecklistEncode/Decode(), esPendienteDocs()` |
| `Models/EstudiantesModel.php` | `checkDuplicados()` ignora vacíos; `insert` `""→NULL`, usuario fallback; `update` igual |
| `Controllers/Estudiantes.php` | `validar()` duros vs diferibles; `matricularNuevo()` pendiente auto + plazo |
| `Models/MatriculaModel.php` | `selectMatricula()` con docs; `setDocumentacion(), setDocumentacionByCiGestion()` tolerantes |
| `Controllers/Matricula.php` | `insertNewMatricula()` acepta pendiente + plazo; badges en `getMatriculaAll()` |
| `Views/Template/Modals/modalEstudiantes.php` | RUDE/email/cel opcionales + bloque pendiente/checklist/compromiso paso 3 |
| `Views/Template/Modals/modalMatricula.php` | Opción `Pendiente de documentos` + checklist + plazo + compromiso |
| `Assets/js/functions_estudiantes.js` | Valida formato solo si hay valor; reset docs en `openModal()` |
| `Assets/js/functions_matricula.js` | `fntView/Edit` muestran precargan plazo; sync check↔select |

## 3. Pasos ejecutados / por ejecutar

- [x] 1. Backup PRE-M01 en `Backups/M01_2026-09-26_Documentacion-Pendiente/db_marista_PRE-M01_2026-09-26.sql` (mysqldump 9.1.0, --routines --events --triggers).
- [x] 2. Código I1 escrito y verificado `php -l` + `node --check` + `validar()` (mínima pasa, email malo falla).
- [x] 3. **Aplicada M01 a BD** el 2026-09-26 vía PDO (columnas) + mysql.exe (SPs). Detalle en `Backups/.../MIGRACION_APLICADA.txt`. Backup POST-M01 de 758 KB.
- [x] 4. Verificación post: 4 columnas YES, SPs 24/25, prueba `test_m01_minima.php` → TEST M01 OK (alta mínima, usuario=CI, 10 pensiones, plazo 2026-11-06, limpieza hecha).
- [ ] 5. Pruebas Secretaría en interfaz (ver Manual de Usuario §2-4).

## 4. Cómo aplicar M01 (cuando autorices)

Opción A — phpMyAdmin: `db_marista > Importar > Tools/migraciones/m01_documentacion_pendiente.sql > Continuar`.
Opción B — yo lo aplico por PDO y te muestro verificación `INFORMATION_SCHEMA`.
Reversión: importar el backup PRE-M01 (ver `Backups/.../LEEME_RESTAURAR.txt`).

## 5. Verificación esperada tras aplicar

```sql
SELECT COLUMN_NAME, IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA='db_marista' AND TABLE_NAME='matricula'
AND COLUMN_NAME IN ('plazo_documentos_hasta','docs_checklist','compromiso_firmado','docs_observacion');
-- 4 filas YES/YES

INSERT prueba mínima (solo CI+nombre): debe dar `dato_guardado` + matrícula `Pendiente_Documentos` con plazo = hoy+30 hábiles.
```

## 6. Registro de cambios de aquí en adelante

- Cada iteración crea `Tools/migraciones/mXX_*.sql` + `Backups/Mxx_fecha_*/db_marista_PRE-Mxx_*.sql` + actualiza `Docs/CAMBIOS.md` y el Manual.
- No se reescribe el módulo; no se toca Pensiones/Notas/SIE.
