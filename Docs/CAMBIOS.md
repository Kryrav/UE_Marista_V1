# CAMBIOS — Sistema Marista (registro vivo)

Formato: `Mxx | fecha | alcance | backup | estado`. Actualizar en cada iteración futura.

| Migración | Fecha | Alcance | Backup | Estado |
|---|---|---|---|---|
| M01 Documentación pendiente 30 días hábiles | 2026-09-26 | RUDE/email/cel diferibles; `Pendiente_Documentos` + plazo + checklist + compromiso; SPs NULL-aware 24/25p | `Backups/M01_2026-09-26_Documentacion-Pendiente/db_marista_PRE-M01_2026-09-26.sql` + POST (758 KB) | ✅ Aplicada y verificada (TEST M01 OK: alta mínima + 10 pensiones + plazo 2026-11-06) |
| M02 (plan) Rematriculación + estados Retirado/Trasladado/Egresado | — | `rematricular()`, workflow estados | `Backups/M02_*/` | Pendiente |
| M03 (plan) Rezago + inclusión + comprobante + búsqueda | — | flags discapacidad/comisión, comprobante matrícula | `Backups/M03_*/` | Pendiente |
