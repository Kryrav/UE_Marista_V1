# CAMBIOS — Sistema Marista (registro vivo)

Formato: `Mxx | fecha | alcance | backup | estado`. Actualizar en cada iteración futura.

| Migración | Fecha | Alcance | Backup | Estado |
|---|---|---|---|---|
| M01 Documentación pendiente 30 días hábiles | 2026-09-26 | RUDE/email/cel diferibles; `Pendiente_Documentos` + plazo + checklist + compromiso; SPs NULL-aware 24/25p | `Backups/M01_2026-09-26_Documentacion-Pendiente/db_marista_PRE-M01_2026-09-26.sql` + POST (758 KB) | ✅ Aplicada y verificada (TEST M01 OK: alta mínima + 10 pensiones + plazo 2026-11-06) |
| SEED demo V1 | 2026-09-26 | `DB_Marista_SEED_DEMO_V1.sql` + `Docs/INSTALACION_DEMO.md`: 10 personas / 4 estudiantes / 4 matrículas / 40 pensiones, todo ficticio `@demo.bo`, clave `marista123` | — (sin datos reales) | ✅ Verificado en `db_marista_demo` temporal (login admin OK, pendiente docs OK), BD demo eliminada |
| M02 Rematriculación + estados terminales | 2026-09-26 | Botón ⏩ + `rematricular/ultimaMatricula`; `Retirado/Trasladado/Egresado` con `motivo_estado` obligatorio; fix transacción huérfana (EXIT HANDLER en 3 SPs + `rollbackLeaked()` en `Mysql.php`); addenda: solo gestión activa salvo rectificación Admin/Director con motivo | `Backups/M02_2026-09-26_Rematriculacion-Estados/` PRE 759 KB + POST 761 KB | ✅ Aplicada y verificada (TEST M02 OK + TEST gestión activa OK, `left=0`) |
| M02 (plan) Rematriculación + estados Retirado/Trasladado/Egresado | — | `rematricular()`, workflow estados | `Backups/M02_*/` | Pendiente |
| M03 (plan) Rezago + inclusión + comprobante + búsqueda | — | flags discapacidad/comisión, comprobante matrícula | `Backups/M03_*/` | Pendiente |
