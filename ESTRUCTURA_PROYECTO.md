# Estructura del Proyecto Marista

Este proyecto utiliza una arquitectura **MVC (Modelo-Vista-Controlador)** personalizada construida en PHP. A continuación se describe el propósito de cada directorio y archivo principal.

## 📁 Estructura de Directorios

### 1. Raíz (`/`)
- **`index.php`**: Es el punto de entrada único de la aplicación.
  - Carga las configuraciones (`Config/Config.php`).
  - Carga los helpers (`Helpers/Helpers.php`).
  - Implementa el enrutamiento básico: captura la URL y determina qué controlador, método y parámetros ejecutar (`Controlador/Metodo/Parametros`).
  - Ejecuta el `Autoload` y `Load` del Core.
- **`.htaccess`**: Configuración del servidor Apache (generalmente para reescritura de URLs amigables).

### 2. `Libraries/` (Librerías y Núcleo)
Contiene las dependencias y el núcleo del framework personalizado.
- **`Core/`**: El corazón del sistema MVC.
  - **`Autoload.php`**: Carga automática de clases para no tener que usar `require` manualmente en cada archivo.
  - **`Load.php`**: Lógica para instanciar el controlador y ejecutar el método solicitado.
  - **`Conexion.php`**: Maneja la conexión a la base de datos (usando PDO).
  - **`Mysql.php`**: Clase padre para los modelos, facilita las consultas CRUD.
  - **`Controllers.php`**: Clase base de la que heredan todos los controladores. Carga los modelos automáticamente.
  - **`Views.php`**: Clase encargada de renderizar las vistas y pasarles información.
- **Librerías Externas**: Contiene herramientas de terceros como `dompdf`, `phpmailer`, etc.

### 3. `Config/` (Configuración)
- **`Config.php`**: Define constantes globales como la URL base (`BASE_URL`), credenciales de base de datos, zona horaria y otras configuraciones del entorno.

### 4. `Controllers/` (Controladores)
Contiene la lógica de negocio. Cada archivo es una clase que extiende de `Controllers`.
- Reciben las peticiones del usuario desde `index.php`.
- Interactúan con los **Modelos** para obtener datos.
- Cargan las **Vistas** para mostrar la respuesta al usuario.

### 4b. `Libraries/Services/` (Servicios — casos de uso)
Convención vigente desde la refactorización SVC:
- **Controllers**: solo HTTP (leer POST/GET, sesión, permisos, CSRF, responder JSON/vista).
- **Services**: reglas de negocio multi-entidad y códigos del dominio
  (`InscripcionService`, `TutorService`, `CursoService`, `CobroService`,
  `MateriaService`, `GestionService`, `AuthService`, `PasswordResetService`,
  `FinanzasService`, `ReciboService`, `UserPolicy`, `PasswordPolicy`,
  `Presenter`, `ServiceResult`). Sin `$_POST` ni `$_SESSION` adentro
  (reciben parámetros; las políticas reciben la sesión como array).
- **Models**: solo persistencia (SQL/SPs).

### 5. `Models/` (Modelos)
Interactúan con la base de datos.
- Cada modelo extiende de `Mysql` (o de una clase base similar en `Libraries/Core`).
- Contiene métodos para seleccionar, insertar, actualizar o eliminar registros de la base de datos.

### 6. `Views/` (Vistas)
Contiene los archivos de interfaz de usuario (HTML/PHP).
- Reciben datos (`$data`) desde el controlador y los muestran al usuario.
- Generalmente organizadas en subcarpetas correspondientes a cada módulo.

### 7. `Helpers/` (Ayudantes)
- **`Helpers.php`**: Funciones globales reutilizables en todo el proyecto (ej. funciones de formateo de texto, depuración `dep()`, limpieza de strings, etc.).

### 8. `Assets/` (Recursos Estáticos)
Almacena archivos públicos accesibles por el navegador:
- `css/`: Hojas de estilo.
- `js/`: Scripts de JavaScript.
- `images/`: Imágenes.
- `fonts/`: Tipografías.

---

## 🔄 Flujo de una Petición

1. El usuario accede a una URL (ej. `marista.com/usuarios/perfil/5`).
2. **`index.php`** recibe la petición.
3. Se descompone la URL en:
   - Controlador: `Usuarios`
   - Método: `perfil`
   - Parámetros: `5`
4. **`Libraries/Core/Load.php`** busca el archivo `Controllers/Usuarios.php`.
5. Se instancia la clase `Usuarios` y se ejecuta el método `perfil($id)`.
6. El método `perfil` solicita datos al Modelo (`Models/UsuariosModel.php`).
7. El método `perfil` carga una vista usando `$this->views->getView(..., $data)`.
8. El usuario ve la página renderizada.
