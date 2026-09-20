<?php 
class Home extends Controllers {
    
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        // Obtener datos dinámicos de la BD
        $colegio = $this->model->getColegioInfo();
        $estadisticas = $this->model->getEstadisticas();
        $cursosDestacados = $this->model->getCursosDestacados();
        $noticias = $this->model->getNoticias();
        $testimonios = $this->model->getTestimonios();
        $eventos = $this->model->getEventosProximos();
        $gestionActiva = $this->model->getGestionActiva();
        
        // Preparar datos para la vista
        $data['page_tag'] = $colegio ? $colegio['nombre_col'] . " - Inicio" : "Colegio Marista SS.CC.";
        $data['page_title'] = $colegio ? $colegio['nombre_col'] : "Colegio Marista";
        $data['page_name'] = "home";
        $data['page_description'] = $colegio ? $colegio['descripcion_col'] : "Institución educativa de excelencia";
        
        // Datos dinámicos
        $data['colegio'] = $colegio;
        $data['estadisticas'] = $estadisticas;
        $data['cursosDestacados'] = $cursosDestacados;
        $data['noticias'] = $noticias;
        $data['testimonios'] = $testimonios;
        $data['eventos'] = $eventos;
        $data['gestionActiva'] = $gestionActiva;
        
        // Año actual para copyright
        $data['anio_actual'] = date('Y');
        
        // Cargar vista
        // $this->views->getView($this, "home", $data);
        $this->views->getView($this, "home_2", $data);
    }
    
    // Página "Sobre Nosotros"
    public function about() {
        $colegio = $this->model->getColegioInfo();
        
        $data['page_tag'] = "Sobre Nosotros - " . ($colegio['nombre_col'] ?? "Colegio Marista");
        $data['page_title'] = "Sobre Nosotros";
        $data['page_name'] = "about";
        $data['colegio'] = $colegio;
        
        $this->views->getView($this, "about", $data);
    }
    
    // Página "Contacto"
    public function contact() {
        $colegio = $this->model->getColegioInfo();
        $contactoEmergencia = $this->model->getContactoEmergencia();
        
        $data['page_tag'] = "Contacto - " . ($colegio['nombre_col'] ?? "Colegio Marista");
        $data['page_title'] = "Contacto";
        $data['page_name'] = "contact";
        $data['colegio'] = $colegio;
        $data['contacto_emergencia'] = $contactoEmergencia;
        
        $this->views->getView($this, "contact", $data);
    }
    
    // Página "Admisiones"
    public function admissions() {
        $colegio = $this->model->getColegioInfo();
        $gestionActiva = $this->model->getGestionActiva();
        
        $data['page_tag'] = "Admisiones - " . ($colegio['nombre_col'] ?? "Colegio Marista");
        $data['page_title'] = "Proceso de Admisión";
        $data['page_name'] = "admissions";
        $data['colegio'] = $colegio;
        $data['gestionActiva'] = $gestionActiva;
        
        $this->views->getView($this, "admissions", $data);
    }
}
?>