# Refactorización SVC — capas Controllers → Services → Models

**Fecha:** 2026-09-28 · **Sin migración** (cero cambios de BD; solo código)

## Problema

Lógica de negocio en controladores + acoplamiento cruzado (`require_once/new XModel`)
+ reglas duplicadas y divergentes (ocupación ×3, `password=CI` ×5, estados de
pensión, gestión activa ×3, badges ×8). Hallado auditando los 20 controladores
contra el caso de `Estudiantes::matricularNuevo`.

## Lo extraído (comportamiento idéntico, verificado)

| Servicio | Reglas absorbidas | Verificación |
|---|---|---|
| `InscripcionService` | matrícula inmediata, rematricular, plan docs, tutores-count, gestión activa | TEST M02 + flujo + REV-MAT OK |
| `CursoService` | ocupación única, cupo, normalizar paralelo, tutor válido | unidades OK |
| `CobroService` | normalizar tipo/ncuota/status + guardar | integración g2 OK |
| `TutorService` | domicilio mismo-dom, armado, guardar | integración g1 OK |
| `MateriaService` | normalizar + guardar (+ traduce SIGNAL duplicado a `exist`) | integración g7 OK |
| `GestionService` | activa/resolver/monto + validar/guardar/cerrar | integración g4 OK |
| `AuthService` | attempt (rate-limit+verify+estado) | stub OK + endpoint wrong-pass OK |
| `PasswordResetService` | request/confirm/reset | stubs OK (36/36 con unidades) |
| `PasswordPolicy` + `UserPolicy` | clave por defecto, login, admin protegido, editar/eliminar | unidades OK |
| `FinanzasService` | estado Pagado/Vencido/Pendiente, puedePagar/Anular, Bs., folio, agregados SQL, pct | golden + g6 OK |
| `ReciboService` | verificabilidad + formatos | código OK |
| `Presenter` | badges idénticos (estado, inscripción, pago) | listas OK |

## Bugs corregidos de paso

- `UsuariosModel::selectPensiones` instanciaba `PensionModel` (inexistente) → el lookup de pensiones del perfil estaba muerto; ahora funciona (20 filas) con estados unificados.
- `Usuarios::getPensiones` anulaba pendientes y perdía `Vencido`; ahora usa las reglas únicas.
- Duplicado de materia abortaba con fatal en `development`; ahora responde `exist`.

## Verificación

- `test_svc_unidades.php`: 44/44 (puras + stubs).
- `test_svc_integracion.php`: g1–g7 contra BD real (tutotes, cobros, cursos, gestión, login, pensiones-perfil, materias).
- Regresión: M02, M03 parcial, flujo óptimo, REV-MAT/EST, CSRF, M05, golden Dashboard/Reportes **byte-idéntico**.
- `php -l` + `node --check` en todo lo tocado. Sin restos (`left=0`).
- Nota de privacidad: `Tools/migraciones/golden_finanzas.json` NO se versiona
  (contiene CI/nombres reales de morosos y sin-tutores); se regenera local con
  `php Tools/migraciones/test_golden_finanzas.php capture` y se compara con
  `... compare`. Igual criterio que `Backups/*.sql`.
