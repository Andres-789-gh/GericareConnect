<?php
class ModeloEnfermedades {

    /*
    Almacena la conexión a la base de datos (objeto PDO) para ser utilizada
    por todos los métodos de la clase.
    */
    private $conn;

    /*
    El constructor se ejecuta al crear una nueva instancia de ModeloEnfermedades.
    Establece la conexión con la base de datos y la asigna a la propiedad $conn.
    */
    public function __construct() {
        /*
        Se utiliza require_once para incluir el archivo de conexión.
        __DIR__ es una constante mágica de PHP que devuelve la ruta del directorio
        del archivo actual, garantizando que la ruta sea siempre correcta sin
        importar desde dónde se llame la clase.
        */
        require_once(__DIR__ . '/../data_base/database.php');
        $this->conn = $conn;
    }

    public function mdlCrearEnfermedad($tabla, $datos) {
        try {
            /*
            Se prepara una sentencia SQL para insertar datos, utilizando marcadores de posición
            con nombre (:nombre_enfermedad) para prevenir inyecciones SQL.
            */
            $stmt = $this->conn->prepare("insert into $tabla(nombre_enfermedad, descripcion_enfermedad, estado) values (:nombre_enfermedad, :descripcion_enfermedad, :estado)");

            /*
            Se vinculan los valores del array $datos a los marcadores de posición de la consulta.
            Se especifica el tipo de dato (PDO::PARAM_STR) para mayor seguridad y rendimiento.
            */
            $stmt->bindParam(":nombre_enfermedad", $datos["nombre_enfermedad"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion_enfermedad", $datos["descripcion_enfermedad"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
            
            /*
            Se ejecuta la sentencia preparada. Si la ejecución es exitosa, el método
            continúa; si falla, PDO lanzará una excepción de tipo PDOException.
            */
            $stmt->execute();
            
            return true;

        } catch (PDOException $e) {
            /*
            Si ocurre una excepción de PDO durante la ejecución, esta es capturada.
            Al hacer 'throw $e;', la excepción original se relanza sin modificarla.
            Esto permite que la capa superior (el controlador) reciba el error
            completo y decida cómo manejarlo (ej. mostrar un mensaje de error específico).
            */
            throw $e;
        }
    }

    /*
     Muestra registros de enfermedades. Puede devolver todos los registros activos
     o filtrar por una columna y valor específicos.
     */
    public function mdlMostrarEnfermedades($tabla, $item, $valor) {
        try {
            if ($item != null) {
                /*
                MEDIDA DE SEGURIDAD: LISTA BLANCA 
                Para prevenir inyección SQL en los nombres de columnas, se valida que
                el valor de $item exista en una lista predefinida de columnas seguras.
                Nunca se debe confiar en la entrada del usuario para nombres de columnas o tablas.
                */
                $columnasPermitidas = ['id_enfermedad', 'nombre_enfermedad'];
                if (!in_array($item, $columnasPermitidas)) {
                    /*
                    Si la columna solicitada no está en la lista blanca, se lanza
                    una excepción para detener la operación y notificar el problema.
                    */
                    throw new InvalidArgumentException("Error de seguridad: Columna no permitida para la búsqueda.");
                }

                /*
                La consulta se prepara de forma segura. El nombre de la tabla y la columna
                ($tabla, $item) ya han sido validados (la tabla de forma implícita y la
                columna con la lista blanca), mientras que el valor del usuario se pasa
                a través de un marcador de posición.
                */
                $stmt = $this->conn->prepare("select * from $tabla where $item = :valor and estado = 'Activo'");
                $stmt->bindParam(":valor", $valor, PDO::PARAM_STR);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC);

            } else {
                /*
                Si no se especifica un $item, se obtienen todos los registros
                de la tabla cuyo estado sea 'Activo'.
                */
                $stmt = $this->conn->prepare("select * from $tabla where estado = 'Activo'");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            /* Se relanza la excepción original para ser manejada por el controlador. */
            throw $e;
        }
    }

    public function mdlEditarEnfermedad($tabla, $datos) {
        try {
            $stmt = $this->conn->prepare("update $tabla set nombre_enfermedad = :nombre_enfermedad, descripcion_enfermedad = :descripcion_enfermedad where id_enfermedad = :id_enfermedad");
            
            $stmt->bindParam(":nombre_enfermedad", $datos["nombre_enfermedad"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion_enfermedad", $datos["descripcion_enfermedad"], PDO::PARAM_STR);
            $stmt->bindParam(":id_enfermedad", $datos["id_enfermedad"], PDO::PARAM_INT);
            
            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function mdlActualizarEstadoEnfermedad($tabla, $datos) {
        try {
            $stmt = $this->conn->prepare("update $tabla set estado = :estado where id_enfermedad = :id_enfermedad");

            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
            $stmt->bindParam(":id_enfermedad", $datos["id_enfermedad"], PDO::PARAM_INT);

            $stmt->execute();
            return true;
            
        } catch (PDOException $e) {
            throw $e;
        }
    }
}