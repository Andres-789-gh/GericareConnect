<?php

    class usuario{

        protected $conn;

        // metodo contructor (inicia las propiedades de la clase) en este caso es para la conexion a BD. 
        public function __construct() {
            // se incluye el archivo donde se encuentra la conexión. 
            //__DIR__" asegura que esta partiendo desde la carpeta actual del archivo usuario.php, para evitar errores por rutas relativas.
            include(__DIR__ . '/../data_base/database.php');
            // asigna la conexión ($conn) a la propiedad protegida "$this->conn" del objeto actual.
            /*
            $this es una referencia al objeto actual de la clase.
            $this->conn accede a la propiedad $conn de ese objeto (declarada como protected $conn;).
            $conn (sin $this) es la variable definida en database.php que contiene la conexión a la base de datos.
            */
            $this->conn = $conn;
        }

        // archivo usuario.php
        public function Login($tipo_documento, $documento_identificacion) {
            try {
                $validar = $this->conn->prepare("
                    select u.*, r.nombre_rol 
                    from tb_usuario u 
                    inner join tb_rol r ON u.id_rol = r.id_rol
                    where u.tipo_documento = ? 
                    and u.documento_identificacion = ? 
                    and u.estado = 'Activo'
                ");
                $validar->execute([$tipo_documento, $documento_identificacion]);
                return $validar->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function Actualizar($datos) {
            try {
                $query = $this->conn->prepare("call actualizar_usuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $query->bindParam(1,  $datos['id_usuario']);
                $query->bindParam(2,  $datos['tipo_documento']);
                $query->bindParam(3,  $datos['documento_identificacion']);
                $query->bindParam(4,  $datos['nombre']);
                $query->bindParam(5,  $datos['apellido']);
                $query->bindParam(6,  $datos['fecha_nacimiento']);
                $query->bindParam(7,  $datos['direccion']);
                $query->bindParam(8,  $datos['correo_electronico']);
                $query->bindParam(9,  $datos['numero_telefono']);
                $query->bindParam(10, $datos['fecha_contratacion']);
                $query->bindParam(11, $datos['tipo_contrato']);
                $query->bindParam(12, $datos['contacto_emergencia']);
                $query->bindParam(13, $datos['parentesco']);
                $query->bindParam(14, $datos['roles']);

                $query->execute();

                return true;

            } catch (Exception $e) {
                throw $e;
            }
        }

        public function obtenerPorId($id_usuario) {
            try {
                $stmt = $this->conn->prepare("
                    SELECT 
                        u.*, 
                        t.numero_telefono, 
                        GROUP_CONCAT(r.nombre_rol) AS roles
                    FROM tb_usuario u
                    LEFT JOIN tb_telefono t ON u.id_usuario = t.id_usuario AND t.estado = 'Activo'
                    LEFT JOIN tb_usuario_rol ur ON u.id_usuario = ur.id_usuario AND ur.estado = 'Activo'
                    LEFT JOIN tb_rol r ON ur.id_rol = r.id_rol
                    WHERE u.id_usuario = ?
                    GROUP BY u.id_usuario
                ");
                $stmt->execute([$id_usuario]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    class cuidador extends usuario{

    }

    class familiar extends usuario {

        public function registrar($datos) {
            try {
                $query = $this->conn->prepare("CALL registrar_familiar(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $query->bindParam(1,  $datos['tipo_documento']);
                $query->bindParam(2,  $datos['documento_identificacion']);
                $query->bindParam(3,  $datos['nombre']);
                $query->bindParam(4,  $datos['apellido']);
                $query->bindParam(5,  $datos['fecha_nacimiento']);
                $query->bindParam(6,  $datos['direccion']);
                $query->bindParam(7,  $datos['correo_electronico']);
                $query->bindParam(8,  $datos['contraseña']);
                $query->bindParam(9,  $datos['numero_telefono']);
                $query->bindParam(10, $datos['parentesco']);

                $query->execute();

                return $query->fetch(PDO::FETCH_ASSOC); // Retorna el id_usuario_creado
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    class administrador extends usuario{

    }

?>
