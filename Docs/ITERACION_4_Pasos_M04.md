# ITERACIÓN 4 — CSRF en módulos Estudiantes/Matrícula/Tutores (M04)

**Fecha:** 2026-09-26 · **Sin migración, sin backup** (solo código; nada que revertir en BD)
**Criterio:** S-04 (validación de entrada / anti-CSRF)

## 1. Qué se hizo

- Token por sesión (`Helpers: csrf_token/csrf_field/csrf_check`, 32 bytes, `hash_equals`).
- Campo oculto en `formEstudiante`, `formNewMatricula`, `formTutor` (los `FormData(form)` lo envían solos).
- Verificación al inicio de cada POST mutante: `setEstudiante`, `saveInclusion`, `delEstudiante`, `insertNewMatricula`, `rematricular`, `delMatricula`, `saveTutor`, `delTutor`. Sin token → `Sesión expirada. Recargue la página.` (no se toca nada).
- JS manual (`rematricular`, 3 bajas, `saveInclusion`) adjunta el token del hidden input.

## 2. Archivos

`Helpers/Helpers.php`, `Controllers/{Estudiantes,Matricula,Tutores}.php`,
`Views/Template/Modals/{modalEstudiantes,modalMatricula,modalTutores}.php`,
`Assets/js/{functions_estudiantes,functions_matricula,functions_tutores}.js`,
`Tools/migraciones/test_m04_csrf.php`.

## 3. Verificación

- `php -l` + `node --check` OK.
- TEST M04 OK: token 64hex estable, `check` ok/malo/vacío; los 4 endpoints mutantes bloquean sin token; con token válido se llega a la validación de campos.
- Sin restos en BD (test no escribe).
