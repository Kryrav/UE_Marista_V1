# Manual de Usuario — Gestión de Estudiantes y Matrícula
**Colegio Marista SS.CC. — Roboré, Bolivia** · Versión 1.1 (incluye M01 Documentación pendiente)
**Usuarios:** Secretaría/Administración (uso diario), Dirección (aprueba), Regencia (cursos), Docentes tutores (consulta)

> Este manual es vivo: cada cambio futuro (M02, M03...) agrega una sección al final y actualiza la portada. Último cambio: M01 2026-09-26.

## 1. Ingreso y roles

1. Abra `http://localhost/_02/marista/login` (o URL del colegio).
2. Ingrese usuario y clave. Si olvida, use Recuperar.
3. Menú: `Estudiantes`, `Matrícula`, `Tutores`, `Cursos`, `Pensiones`.
   - Secretaría: crear/editar/matricular.
   - Dirección: todo + cerrar gestión.
   - Docente tutor: solo ver su curso (botón Ver/Ficha, sin Editar/Borrar).

## 2. Registrar estudiante nuevo (3 pasos)

`Estudiantes > Nuevo`

**Paso 1 Personales (foto opcional JPG/PNG máx 2 MB):**
- Obligatorios siempre: `C.I. *, Nombres *, Apellidos *, Sexo *, Fecha nacimiento *`.
- Diferibles (desde M01, ya NO bloquean): `RUDE, Email, Nº Celular`.
  Si no los tiene, déjelos vacíos y el sistema los pedirá después.

**Paso 2 Académicos:**
- `Estudiante: Nuevo/Antiguo/Retirado`, `Colegio procedencia`, `País/Ciudad/Provincia`, `Emergencia`, `Estado Activo/Inactivo`, legajo `Folio (vacío=auto), Estante, Gaveta, Estado`.

**Paso 3 Acceso y matrícula:**
- Aviso: la clave inicial es el C.I.; se cambia en `Usuarios`.
- `☑ Matricular de una vez` + `Paralelo` + `Tipo Regular/Becado`.
- **NUEVO M01 — Documentación pendiente:** tílde `Documentación pendiente` si falta `Cert. nacimiento / RUDE / Solicitud`. Marque lo entregado, deje `Compromiso firmado` tildado y agregue observación si quiere. `Guardar`.

Resultado: `Estudiante registrado... Matriculado en gestión 2026... Documentación pendiente hasta 2026-XX-XX`.

## 3. Inscribir sin documentos completos (norma Bolivia)

- La inscripción **no se bloquea** en ningún mes del año.
- Al guardar con RUDE/email/celular vacíos, el sistema pone `Pendiente_Documentos` automáticamente.
- Plazo: **30 días hábiles** (lun–vie) desde la matrícula. El apoderado firma compromiso.
- En `Matrícula` verá badge ámbar `Pendiente docs (12 d)` o `(vencido 3d)`. Pida los papeles antes del vencimiento.
- Cuando entreguen: `Matrícula > Editar >` tílde checklist completo > cambie a `Confirmado` > Guardar. El plazo se libera.

## 4. Matricular desde el módulo Matrícula

`Matrícula > Matricular Estudiante`: `C.I. + Año lectivo + Curso + Tipo + Folio (opcional) + Confirmación [Confirmado/Inscrito/Pendiente de documentos]`.
Si elige Pendiente, complete checklist + `Plazo hasta` (vacío = auto 30 hábiles) + `Compromiso` + observación. Un estudiante = una matrícula por gestión (si repite, avisa `ya matriculado`).

## 5. Buscar, filtrar y ficha

- Tabla `Estudiantes`: buscador global (CI, RUDE, nombre), filtros `Curso actual`, `Estado Activos/Inactivos`, `☑ Solo sin folio`. Botones Copiar/Excel/PDF/CSV.
- Acciones por fila: 👁 Ficha, ✏️ Editar, 🗑 Dar de baja, 👥 Tutores.
- **Ficha 360°:** Datos + Tutores (conteo) + Matrículas por gestión + Pensiones (pagadas/deuda) + legajo. Botones `Carnet QR` e `Historial` (imprimibles).

## 6. Tutores / apoderados

- Desde fila `👥` verá tutores o aviso `sin tutor`. Para agregar: `Tutores > Nuevo >` busque CI (si existe se prellena, no duplique) `> seleccione estudiante > Parentesco Padre/Madre/Tutor > Guardar`.
- Se permiten varios (padre + madre + tutor). `☑ Mismo domicilio` copia la dirección del estudiante.

## 7. Editar, corregir RUDE/email después y dar de baja

- `✏️` corrige datos; si completa RUDE/email pendientes, guarde y luego confirme la matrícula (paso 3).
- `🗑 Dar de baja`: bloquea acceso y tutores. Si tiene matrícula activa, primero dé de baja la matrícula en `Matrícula > Eliminar`. Eliminados no aparecen (borrado lógico).

## 8. Listas por curso y comprobantes

- `Cursos > Ver > Lista / Imprimir` nómina del paralelo.
- `Estudiantes > Ficha > Historial` (pagos de la matrícula) y `Carnet QR` (verificable en `Verificar`).
- Comprobante de matrícula anual (M03, pendiente): por ahora el historial + ficha son respaldo.

## 9. Preguntas frecuentes

- **¿Puedo inscribir en octubre?** Sí, todo el año.
- **¿Sin RUDE?** Sí, queda pendiente 30 días hábiles.
- **¿Sin email/celular?** Sí, el usuario será el CI.
- **¿CI duplicado?** El sistema avisa `ya registrado`; busque al estudiante y rematricule, no lo duplique.
- **¿Vencido?** Sale `vencido Nd` en rojo; exija documentos y pase a Confirmado.
- **¿Foto no sube?** Debe ser JPG/PNG/WEBP ≤2 MB.

## 10. Soporte y resguardo

- Ante error, anote hora + CI + mensaje y avise a sistemas. No borre matrículas con pensiones cobradas.
- Backup PRE-M01 guardado en `Backups/M01_2026-09-26_Documentacion-Pendiente/`. Restauración solo por sistemas (ver LEEME).

## Historial del manual (actualizar aquí cada cambio futuro)

- v1.1 2026-09-26 M01: diferibles, pendiente 30h, checklist, badges, plazo auto.
- v1.0 Base: alta 3 pasos, matrícula, ficha, tutores, legajo.
- Próximo M02: rematriculación en 1 clic + Retirado/Trasladado/Egresado. M03: rezago, discapacidad, comprobante matrícula.
