<?php

class ModeloMedicamentos {
    
    private $conn;

    /* Constructor para la conexión a la base de datos. */
    public function __construct() {
        require(__DIR__ . '/../data_base/database.php');
        $this->conn = $conn;
    }

    /* Crea un nuevo medicamento en la base de datos. */
    public function mdlCrearMedicamento($tabla, $datos) {
        try {
            $stmt = $this->conn->prepare("insert into $tabla(nombre_medicamento, descripcion_medicamento, estado) values (:nombre_medicamento, :descripcion_medicamento, :estado)");
            $stmt->bindParam(":nombre_medicamento", $datos["nombre_medicamento"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion_medicamento", $datos["descripcion_medicamento"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
            
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Muestra los medicamentos, opcionalmente filtrando por una columna y valor. */
    public function mdlMostrarMedicamentos($tabla, $item, $valor) {
        try {
            if ($item != null) {
                // Lista blanca de columnas para prevenir inyección SQL.
                $columnasPermitidas = ['id_medicamento', 'nombre_medicamento'];
                if (!in_array($item, $columnasPermitidas)) {
                    throw new InvalidArgumentException("Columna no válida para la búsqueda.");
                }

                $stmt = $this->conn->prepare("select * from $tabla where $item = :valor AND estado = 'Activo'");
                $stmt->bindParam(":valor", $valor, PDO::PARAM_STR);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $stmt = $this->conn->prepare("select * from $tabla where estado = 'Activo'");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Edita la información de un medicamento existente. */
    public function mdlEditarMedicamento($tabla, $datos) {
        try {
            $stmt = $this->conn->prepare("update $tabla set nombre_medicamento = :nombre_medicamento, descripcion_medicamento = :descripcion_medicamento where id_medicamento = :id_medicamento");
            $stmt->bindParam(":nombre_medicamento", $datos["nombre_medicamento"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion_medicamento", $datos["descripcion_medicamento"], PDO::PARAM_STR);
            $stmt->bindParam(":id_medicamento", $datos["id_medicamento"], PDO::PARAM_INT);
            
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Actualiza el estado de un medicamento para un borrado lógico. */
    public function desactivarEstadoMedicamento($tabla, $datos) {
        try {
            $stmt = $this->conn->prepare("update $tabla set estado = :estado where id_medicamento = :id_medicamento");
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
            $stmt->bindParam(":id_medicamento", $datos["id_medicamento"], PDO::PARAM_INT);

            $stmt->execute();
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>
