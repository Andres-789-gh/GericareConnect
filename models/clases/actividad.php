<?php
// Importa el script de conexión a la BD. `_once` evita inclusiones múltiples.
require_once __DIR__ . '/../data_base/database.php';

// Define la clase que encapsula toda la lógica de negocio para las actividades.
class Actividad {
    // Propiedad privada para almacenar el objeto de conexión (PDO).
    // Su acceso está restringido solo a métodos dentro de esta clase.
    private $conn;

    /**
     * Constructor: se ejecuta automáticamente al instanciar la clase (ej: new Actividad()).
     * Su objetivo es establecer la conexión a la base de datos para el objeto.
     */
    public function __construct() {
        // Importa la variable de conexión '$conn' del ámbito global al local de esta función.
        // Es un paso necesario para acceder a la conexión definida en 'database.php'.
        global $conn;
        
        // Asigna la conexión a la propiedad de la clase.
        // Esto permite que los demás métodos (registrar, actualizar, etc.) la utilicen a través de '$this->conn'.
        $this->conn = $conn;
    }

    /*
     Registra una nueva actividad.
     Lanza una excepción si ocurre un error en la base de datos.
     */
    public function registrar($datos) {
        try {
            $stmt = $this->conn->prepare("call registrar_actividad(?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $datos['id_paciente'], $_SESSION['id_usuario'], $datos['tipo_actividad'],
                $datos['descripcion_actividad'], $datos['fecha_actividad'], $datos['hora_inicio'],
                $datos['hora_fin']
            ]);
            return true;
        } catch (Exception $e) {
            // Relanza la excepción original para que el controlador la maneje.
            throw $e;
        }
    }

    /*
     Consulta actividades con filtros opcionales.
     Lanza una excepción si ocurre un error en la base de datos.
     */
    public function consultar($busqueda = null, $estado_filtro = null) {
        try {
            $stmt = $this->conn->prepare("call consultar_actividades(?, ?)");
            $stmt->execute([$busqueda, $estado_filtro]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /*
     Obtiene una actividad específica por su ID.
     Lanza una excepción si la consulta subyacente falla.
     */
    public function obtenerPorId($id_actividad) {
        try {
            // Llama a consultar, que ya tiene su propio manejo de errores.
            $actividades = $this->consultar(null, null); 
            foreach ($actividades as $actividad) {
                if ($actividad['id_actividad'] == $id_actividad) {
                    return $actividad;
                }
            }
            // Si no se encuentra, devuelve null. (Esto no es un error de BD)
            return null;
        } catch (Exception $e) {
            // Si el método consultar() lanzó un error, lo atrapamos y relanzamos.
            throw $e;
        }
    }

    /*
     Actualiza una actividad existente.
     Lanza una excepción si ocurre un error en la base de datos.
     */
    public function actualizar($datos) {
        try {
            $stmt = $this->conn->prepare("call actualizar_actividad(?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $datos['id_actividad'], $datos['id_paciente'], $datos['tipo_actividad'],
                $datos['descripcion_actividad'], $datos['fecha_actividad'], $datos['hora_inicio'],
                $datos['hora_fin']
            ]);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /*
     Elimina una actividad.
     Lanza una excepción si ocurre un error en la base de datos.
     */
    public function eliminar($id_actividad) {
        try {
            $stmt = $this->conn->prepare("call eliminar_actividad(?)");
            $stmt->execute([$id_actividad]);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /*
     Consulta actividades asignadas a un cuidador específico.
     Lanza una excepción si ocurre un error en la base de datos.
     */
    public function consultarPorCuidador($id_cuidador, $busqueda = null, $estado_filtro = null) {
        try {
            $stmt = $this->conn->prepare("call consultar_actividades_cuidador(?, ?, ?)");
            $stmt->execute([$id_cuidador, $busqueda, $estado_filtro]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /*
     Marca una actividad como completada.
     Lanza una excepción si ocurre un error en la base de datos.
     */
    public function marcarComoCompletada($id_actividad, $id_cuidador) {
        try {
            $stmt = $this->conn->prepare("call completar_actividad(?, ?)");
            $stmt->execute([$id_actividad, $id_cuidador]);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>