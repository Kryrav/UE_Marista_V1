# Contexto del Proyecto: Sistema de Gestión Escolar Marista SS.CC.


## 1. Resumen del Proyecto
- **Nombre**: Colegio Marista SS.CC. (Sistema de Gestión Escolar).
- **Objetivo**: Administrar procesos académicos y administrativos (Matrículas, Notas, Pensiones, Cursos, Estudiantes).
- **Tecnologías**: 
  - **Backend**: PHP (MVC Personalizado).
  - **Base de Datos**: MySQL (`db_mr_sistm`).
  - **Frontend**: HTML5, CSS3, Vanilla JS (sin frameworks reactivos).
  - **Librerías Clave**: TCPDF/Fpdf (Reportes), PHPMailer (Correos).

## 2. Arquitectura de Software
Utilizamos un **MVC (Modelo-Vista-Controlador)** propio construido desde cero.

### Flujo de Trabajo
1.  **Router (`index.php`)**: Captura la URL `clase/metodo/parametros`.
2.  **Controlador (`Controllers/`)**: Procesa la lógica de negocio.
3.  **Modelo (`Models/`)**: Realiza consultas SQL a la BD.
4.  **Vista (`Views/`)**: Renderiza el HTML con los datos (`$data`).

### Directorios Clave
- `Config/Config.php`: Constantes globales (DB, URL, info de la empresa).
- `Libraries/Core/`:
  - `Conexion.php`: Singleton PDO para MySQL.
  - `Mysql.php`: Wrapper para CRUD (`insert`, `select`, `select_all`, `update`, `delete`).
  - `Controllers.php`: Carga automáticamente el modelo asociado (`$this->model`).
  - `Views.php`: Renderiza vistas (`$this->views->getView($this, "vista", $data)`).

## 3. Módulos Identificados
El sistema cuenta con los siguientes módulos principales (basado en Controladores):
- **Acceso**: `Login`, `Home` (Sitio público), `Dashboard` (Panel interno).
- **Usuarios**: `Usuarios`, `Roles`, `Permisos` (ACL).
- **Académico**: `Estudiantes`, `Cursos`, `Materias`, `Matricula`.
- **Administrativo**: `Pensiones`, `Cobros`, `Gestion` (Año escolar).

## 4. Convenciones de Código
- **Clases**: UpperCamelCase (ej. `HomeModel`, `Usuarios`).
- **Métodos/Variables**: camelCase (ej. `getUsuarios`, `$strNombre`).
- **SQL**: Uso de métodos del Core (`$this->insert($query, $arrValues)`).
- **Vistas**: Archivos `.php` dentro de `Views/Modulo/archivo.php`. Las variables pasan en un array `$data`.

### Doctrina de estados (separación vida académica vs acceso)
- `estudiante.status`: `1=Activo`, `2=Inactivo`, `0=Eliminado` (soft delete, oculto en listas).
  Solo `1` puede matricularse (validado en `matricular_estudiante_y_generar_pensiones`)
  y aparecer en selectores académicos. **No restringe el acceso al sistema.**
- `persona.status`: `1=con acceso`, `0=bloqueado`. Se gestiona **solo** desde el
  módulo **Usuarios** (más alta/baja). El login exige `status=1`.
- El módulo **Usuarios** lista todas las personas (`status IN (1,2)`, todos los roles)
  para gestionar el acceso sin importar su estado académico.

## 5. Instrucciones para la IA
Al generar código para este proyecto:
1.  **Seguir el patrón MVC**: No mezclar lógica SQL en controladores ni HTML en modelos.
2.  **Usar el Core**: Utilizar `$this->model->select()` etc., no usar `mysqli_query` nativo.
3.  **Respetar Config.php**: Usar `BASE_URL` para enlaces y recursos.
4.  **Estilo**: Mantener la indentación y estructura existente.
