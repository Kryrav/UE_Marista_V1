# Instalación demo para desarrolladores (datos inventados)

Base limpia **sin datos reales**: todos los CI/RUDE/emails/teléfonos son ficticios
(series `10000001…`, `20000001…`, `RUDE-DEMO-…`, `@demo.bo`). Clave única: **`marista123`**.

## Requisitos

- MySQL 8/9 o MariaDB + Apache + PHP 8.3 (WAMP/XAMPP).
- Clonar el repo y apuntar `Config/Config.php` → `BASE_URL` a tu ruta local.

## Pasos (en orden)

```bash
# 1. Crear BD vacía
mysql -u root -e "CREATE DATABASE db_marista_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Estructura base
mysql -u root db_marista_demo < DB_Marista_Script_V2.sql

# 3. Migración M01 (documentación pendiente).
#    MySQL 8/9 NO acepta ADD COLUMN IF NOT EXISTS: si falla con error 1064,
#    aplicar con chequeo previo (ver Docs/ITERACION_1_Pasos_M01.md §4).
mysql -u root db_marista_demo < Tools/migraciones/m01_documentacion_pendiente.sql

# 4. Datos demo (verificado: 10 personas, 4 estudiantes, 4 matrículas, 40 pensiones)
mysql -u root db_marista_demo < DB_Marista_SEED_DEMO_V1.sql
```

Luego en `Config/Config.php` cambia `DB_NAME` a `db_marista_demo` y abre `/login`.

## Credenciales demo (clave `marista123` para todos)

| Usuario | Rol | Permisos |
|---|---|---|
| `admin` | Administrador | Todo |
| `direccion` | Director | Lectura todo |
| `secretaria` | Secretario | Estudiantes + Matrícula + Tutores |
| `docente` | Docente | Ver cursos/estudiantes |
| `contador` | Contador | Pensiones + Cobros |
| `ana.demo@demo.bo` | Estudiante | Dashboard (ficha vía QR/verificar) |
| `20000003` | Estudiante pendiente docs | Dashboard (entra con **CI**, sin email) |
| `madre.demo@demo.bo` | Tutor | Dashboard |

## Casos para probar M01

1. `secretaria` → `Estudiantes > Nuevo` solo con CI+nombres+FN → guarda y crea matrícula `Pendiente_Documentos` con plazo auto.
2. `Estudiantes` → ver `20000003 María Demo Tres`: sin RUDE/email, `usuario = CI`.
3. `Matrícula` → badge ámbar `Pendiente docs (N d)` en la fila de `20000003`.
4. Completar RUDE/email en `Editar` → pasar matrícula a `Confirmado` → plazo se libera.

## Notas

- No importar este seed en la BD de producción (hace `DELETE` previo).
- Los dumps `Backups/*.sql` con datos reales **no se versionan** (ver `.gitignore`).
