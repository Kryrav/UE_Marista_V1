<?php 
class HomeModel extends Mysql {
    
    public function __construct() {
        parent::__construct();
    }
    
    // Obtener información del colegio
    public function getColegioInfo() {
        $sql = "SELECT nombre_col, direccion_col, telefono_col, correo_col, 
                       sitio_web_col, director_col, descripcion_col, 
                       horario_atencion_col, nro_estudiantes_col, nro_docentes_col
                FROM colegio 
                WHERE status = 1 
                LIMIT 1";
        return $this->select($sql);
    }
    
    // Obtener estadísticas generales
    public function getEstadisticas() {
        $sql = "SELECT 
                (SELECT COUNT(*) FROM estudiante WHERE status = 1) as total_estudiantes,
                (SELECT COUNT(*) FROM docente WHERE status = 1) as total_docentes,
                (SELECT COUNT(*) FROM paralelo WHERE status = 1) as total_cursos,
                (SELECT COUNT(*) FROM administrativo WHERE status = 1) as total_administrativos,
                (SELECT COUNT(*) FROM materia WHERE status = 1) as total_materias";
        return $this->select($sql);
    }
    
    // Obtener últimas noticias o anuncios
    public function getNoticias($limit = 3) {
        $sql = "SELECT titulo, contenido, fecha_publicacion, imagen 
                FROM noticias 
                WHERE status = 1 
                ORDER BY fecha_publicacion DESC 
                LIMIT ?";
        return $this->select_all($sql, [$limit]);
    }
    
    // Obtener eventos próximos
    public function getEventosProximos($limit = 5) {
        $sql = "SELECT titulo, descripcion, fecha_inicio, fecha_fin, lugar 
                FROM eventos 
                WHERE status = 1 AND fecha_inicio >= CURDATE()
                ORDER BY fecha_inicio ASC 
                LIMIT ?";
        return $this->select_all($sql, [$limit]);
    }
    
    // Obtener cursos destacados (con más estudiantes)
    public function getCursosDestacados($limit = 6) {
        $sql = "SELECT p.nivel, p.grado, p.sigla, p.tutor, 
                       COUNT(m.id_estudiante) as total_inscritos
                FROM paralelo p
                LEFT JOIN matricula m ON p.id_paralelo = m.id_paralelo 
                     AND m.gestion = YEAR(CURDATE())
                WHERE p.status = 1
                GROUP BY p.id_paralelo
                ORDER BY total_inscritos DESC
                LIMIT ?";
        return $this->select_all($sql, [$limit]);
    }
    
    // Obtener testimonios de padres/estudiantes
    public function getTestimonios($limit = 3) {
        $sql = "SELECT nombre, testimonio, cargo, foto 
                FROM testimonios 
                WHERE status = 1 
                ORDER BY fecha_creacion DESC 
                LIMIT ?";
        return $this->select_all($sql, [$limit]);
    }
    
    // Verificar si hay gestión activa
    public function getGestionActiva() {
        $sql = "SELECT gestion, gestion_l, monto_pension 
                FROM gestion 
                WHERE status = 1 
                LIMIT 1";
        return $this->select($sql);
    }
    
    // Obtener contacto de emergencia
    public function getContactoEmergencia() {
        $sql = "SELECT telefono, email, direccion 
                FROM configuracion 
                WHERE tipo = 'emergencia' 
                LIMIT 1";
        return $this->select($sql);
    }
}
?>