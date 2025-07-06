<?php
// Establecer la cabecera para respuestas JSON en peticiones AJAX
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
}

require_once __DIR__ . '/../../../models/clases/historia_clinica_modelo.php';

class HistoriaClinicaController
{
    private $model;

    public function __construct()
    {
        $this->model = new HistoriaClinica();
    }

    public function registrarHistoriaClinica($datos)
    {
        $this->model->setIdPaciente($datos['id_paciente']);
        $this->model->setFechaCreacion($datos['fecha_creacion']);
        $this->model->setMotivoConsulta($datos['motivo_consulta']);
        $this->model->setAntecedentesMedicos($datos['antecedentes_medicos']);
        $this->model->setExamenFisico($datos['examen_fisico']);
        $this->model->setDiagnostico($datos['diagnostico']);
        $this->model->setPlanTratamiento($datos['plan_tratamiento']);
        
        $resultado = $this->model->registrarHistoriaClinica();
        
        // Redirigir de vuelta a la lista con un mensaje
        header("Location: ../../../views/admin/html_admin/historia_clinica.php?status=" . ($resultado ? "success" : "error"));
        exit();
    }

    public function editarHistoriaClinica($datos)
    {
        $this->model->setIdHistoriaClinica($datos['id_historia_clinica']);
        $this->model->setFechaCreacion($datos['fecha_creacion']);
        $this->model->setMotivoConsulta($datos['motivo_consulta']);
        $this->model->setAntecedentesMedicos($datos['antecedentes_medicos']);
        $this->model->setExamenFisico($datos['examen_fisico']);
        $this->model->setDiagnostico($datos['diagnostico']);
        $this->model->setPlanTratamiento($datos['plan_tratamiento']);

        $resultado = $this->model->editarHistoriaClinica();
        
        header("Location: ../../../views/admin/html_admin/historia_clinica.php?status=" . ($resultado ? "updated" : "error"));
        exit();
    }

    public function consultarHistoriasClinicas()
    {
        return $this->model->consultarHistoriasClinicas();
    }

    public function obtenerHistoriaClinicaPorId($id)
    {
        return $this->model->obtenerHistoriaClinicaPorId($id);
    }
    
    public function buscarHistoriasClinicas($term)
    {
        return $this->model->buscarHistoriasClinicas($term);
    }
}


// --- Lógica para peticiones ---

// Petición AJAX para la búsqueda
if (isset($_GET['search'])) {
    $controller = new HistoriaClinicaController();
    $searchTerm = trim($_GET['search']);
    $resultados = $controller->buscarHistoriasClinicas($searchTerm);
    echo json_encode($resultados);
    exit();
}

// Petición POST desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $controller = new HistoriaClinicaController();
    
    switch ($_POST['action']) {
        case 'registrar':
            $controller->registrarHistoriaClinica($_POST);
            break;
        case 'editar':
            $controller->editarHistoriaClinica($_POST);
            break;
    }
}
