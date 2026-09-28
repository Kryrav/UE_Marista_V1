# CAMBIOS — Sistema Marista (registro vivo)

Formato: `Mxx | fecha | alcance | backup | estado`. Actualizar en cada iteración futura.

| Migración | Fecha | Alcance | Backup | Estado |
|---|---|---|---|---|
| M01 Documentación pendiente 30 días hábiles | 2026-09-26 | RUDE/email/cel diferibles; `Pendiente_Documentos` + plazo + checklist + compromiso; SPs NULL-aware 24/25p | `Backups/M01_2026-09-26_Documentacion-Pendiente/db_marista_PRE-M01_2026-09-26.sql` + POST (758 KB) | ✅ Aplicada y verificada (TEST M01 OK: alta mínima + 10 pensiones + plazo 2026-11-06) |
| SEED demo V1 | 2026-09-26 | `DB_Marista_SEED_DEMO_V1.sql` + `Docs/INSTALACION_DEMO.md`: 10 personas / 4 estudiantes / 4 matrículas / 40 pensiones, todo ficticio `@demo.bo`, clave `marista123` | — (sin datos reales) | ✅ Verificado en `db_marista_demo` temporal (login admin OK, pendiente docs OK), BD demo eliminada |
| M02 Rematriculación + estados terminales | 2026-09-26 | Botón ⏩ + `rematricular/ultimaMatricula`; `Retirado/Trasladado/Egresado` con `motivo_estado` obligatorio; fix transacción huérfana (EXIT HANDLER en 3 SPs + `rollbackLeaked()` en `Mysql.php`); addenda: solo gestión activa salvo rectificación Admin/Director con motivo | `Backups/M02_2026-09-26_Rematriculacion-Estados/` PRE 759 KB + POST 761 KB | ✅ Aplicada y verificada (TEST M02 OK + TEST gestión activa OK, `left=0`) |
| M03 Inclusión + rezago + comprobante + búsqueda + auditoría | 2026-09-26 | Tabla `estudiante_inclusion`; `calculaRezago()` + reporte Comisión; comprobante imprimible; `buscar?q` + índice apellidos; `created_by/updated_by` + `id_user` real | `Backups/M03_2026-09-26_Rezago-Inclusion-Comprobante/` PRE 761 KB + POST 770 KB | ✅ Aplicada y verificada (TEST M03 OK, `left=0`) |
| M04 CSRF Estudiantes/Matrícula/Tutores | 2026-09-26 | Token por sesión + campo oculto + verificación en 8 POST mutantes (+ JS manual) | — (solo código) | ✅ Verificado (TEST M04 OK, bloquea sin token) |
| REV-Estudiantes | 2026-09-28 | Revisión funcional total del módulo: `matricularNuevo` ahora registra auditoría (`created_by`/`id_user`); `setEstudiante` defensivo (`??`); `fntSaveInclusion` best-effort + guard de red en alta | — (solo código) | ✅ Batería OK: validar 5/5, alta+matrícula+auditoría, lista 205 con keys, ficha 360°, baja bloqueada, historial/carnet/rezagados(121)/comprobante imprimibles, buscar, inclusión, paralelos |
| M02 (plan) Rematriculación + estados Retirado/Trasladado/Egresado | — | `rematricular()`, workflow estados | `Backups/M02_*/` | Pendiente |
| M03 (plan) Rezago + inclusión + comprobante + búsqueda | — | flags discapacidad/comisión, comprobante matrícula | `Backups/M03_*/` | Pendiente |
