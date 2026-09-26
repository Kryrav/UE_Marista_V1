# ITERACIÓN 3 — Inclusión, rezago, comprobante, búsqueda, auditoría (M03)

**Fecha:** 2026-09-26 · **BD:** `db_marista` (MySQL 9.1.0)
**Criterios:** N-03 (rezago 2+), N-04 (inclusión/paralela), U-05 (comprobante), F-07 (búsqueda), D-04/S-03 (auditoría)
**Riesgo:** bajo (tabla nueva separada, columnas NULL, sin tocar SPs)

## 1. Qué se hizo

- **Inclusión (N-04):** tabla `estudiante_inclusion` 1-1 (discapacidad, tipo, adaptaciones, centro especial, matrícula paralela, requiere comisión). Sección en el paso 2 del modal (no bloquea), tira en la ficha, guardado tras el alta/edición. Sin cambios en SPs.
- **Rezago (N-03):** `calculaRezago()` (Inicial 4-5a, Primaria N = N+5; alerta ≥2a) + `Estudiantes::rezagados` (vista imprimible para Comisión Técnica con firmas) + botón `Rezago` en el módulo. Regla: solo activos con curso en gestión activa.
- **Comprobante (U-05):** `Matricula::comprobante(id)` + vista imprimible (datos, estado/motivo, docs pendientes, totales, 3 firmas). Botón 🖨 en tabla + enlace en ficha.
- **Búsqueda (F-07):** `Estudiantes::buscar?q` JSON top 20 (CI/RUDE/nombre) + índice `idx_persona_apellido`.
- **Auditoría (D-04/S-03):** `created_by/updated_by/updated_at` en estudiante y matrícula; `id_user` de matrícula ahora guarda el cajero real (el SP lo dejaba en 1). Se registra en alta, edición, matrícula y rematriculación.

## 2. Archivos

| Archivo | Cambio |
|---|---|
| `Tools/migraciones/m03_inclusion_auditoria_indices.sql` | Migración M03 |
| `Tools/migraciones/test_m03_rezago_inclusion.php` | TEST M03 OK |
| `Helpers/Helpers.php` | `calculaRezago()` |
| `Models/EstudiantesModel.php` | `getInclusion/saveInclusion/buscarEstudiantes/rezagoCandidates`, auditoría, motivo/plazo en historial |
| `Models/MatriculaModel.php` | auditoría + `id_user` real |
| `Controllers/Estudiantes.php` | `buscar/rezagados/getInclusion/saveInclusion`, `$uid` en alta/edición |
| `Controllers/Matricula.php` | `comprobante`, `$uid` en alta/edición/rematricular, botón 🖨 |
| `Views/Estudiantes/rezagados.php`, `Views/Matricula/comprobante.php` | Hojas imprimibles |
| `Views/Template/Modals/modalEstudiantes.php` | Sección inclusión + tira en ficha |
| `Views/Estudiantes/estudiantes.php` | Botón Rezago |
| `Assets/js/functions_estudiantes.js` | guardado/carga/tira de inclusión, comprobante en ficha |
| `Assets/js/functions_matricula.js` | `fntComprobante()` |

## 3. Pasos

- [x] Backup PRE-M03 (761 KB) + POST-M03 (770 KB).
- [x] Migración aplicada (tabla + 6 columnas + índice).
- [x] `php -l` + `node --check` OK. TEST M03 OK, `left=0`.
- [ ] Prueba Secretaría en UI (Manual v1.3).
