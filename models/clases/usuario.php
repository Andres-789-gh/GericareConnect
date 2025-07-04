<?php

    class usuario{

        protected $conn;

        // metodo contructor (inicia las propiedades de la clase) en este caso es para la conexion a BD. 
        public function __construct() {
            /* se incluye el archivo donde se encuentra la conexión. 
            __DIR__" asegura que esta partiendo desde la carpeta actual del archivo usuario.php, para evitar errores por rutas relativas. */
            include(__DIR__ . '/../data_base/database.php');
            /* asigna la conexión ($conn) a la propiedad protegida "$this->conn" del objeto actual.
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
                    inner join tb_rol r on u.id_rol = r.id_rol
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
                $query = $this->conn->prepare("CALL actualizar_usuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $query->bindParam(1,  $datos['id_usuario']);
                $query->bindParam(2,  $datos['tipo_documento']);
                $query->bindParam(3,  $datos['documento_identificacion']);
                $query->bindParam(4,  $datos['nombre']);
                $query->bindParam(5,  $datos['apellido']);
                $query->bindParam(6,  $datos['direccion']);
                $query->bindParam(7,  $datos['correo_electronico']);
                $query->bindParam(8,  $datos['numero_telefono']);
                $query->bindParam(9,  $datos['fecha_contratacion']);
                $query->bindParam(10, $datos['tipo_contrato']);
                $query->bindParam(11, $datos['contacto_emergencia']);
                $query->bindParam(12, $datos['fecha_nacimiento']);
                $query->bindParam(13, $datos['parentesco']);
                $query->bindParam(14, $datos['nombre_rol']); 

                $query->execute();

                return true;

            } catch (Exception $e) {
                throw $e;
            }
        }

        public function obtenerPorId($id_usuario) {
            try {
                $stmt = $this->conn->prepare("
                    select 
                        u.*, 
                        t.numero_telefono, 
                        r.nombre_rol as roles
                    from tb_usuario u
                    left join tb_telefono t on u.id_usuario = t.id_usuario and t.estado = 'Activo'
                    left join tb_rol r on u.id_rol = r.id_rol
                    where u.id_usuario = ?
                ");
                $stmt->execute([$id_usuario]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                throw $e;
            }
        }
        
        // ======================================================
        // ||       CAMBIO PARA EL FORMULARIO DE PACIENTES       ||
        // ======================================================
        // Esta función busca a todos los usuarios que tengan un rol específico 
        // para rellenar la lista desplegable de familiares en el formulario de pacientes.
        public function obtenerUsuariosPorRol($nombre_rol) {
            try {
                $query = $this->conn->prepare("
                    SELECT u.id_usuario, u.nombre, u.apellido 
                    FROM tb_usuario u
                    JOIN tb_rol r ON u.id_rol = r.id_rol
                    WHERE r.nombre_rol = ? AND u.estado = 'Activo'
                    ORDER BY u.apellido, u.nombre
                ");
                $query->execute([$nombre_rol]);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log("Error al obtener usuarios por rol: " . $e->getMessage());
                return [];
            }
        }
    }

    class familiar extends usuario {

        public function registrar($datos) {
            try {
                $query = $this->conn->prepare("CALL registrar_familiar(?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $query->bindParam(1,  $datos['tipo_documento']);
                $query->bindParam(2,  $datos['documento_identificacion']);
                $query->bindParam(3,  $datos['nombre']);
                $query->bindParam(4,  $datos['apellido']);
                $query->bindParam(5,  $datos['direccion']);
                $query->bindParam(6,  $datos['correo_electronico']);
                $query->bindParam(7,  $datos['contraseña']);
                $query->bindParam(8,  $datos['numero_telefono']);
                $query->bindParam(9,  $datos['parentesco']);

                $query->execute();

                return $query->fetch(PDO::FETCH_ASSOC); // Retorna el id_usuario_creado
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function consultarPacientesFamiliar($id_familiar, $busqueda = '') {
            try {
                $query = $this->conn->prepare("call consultar_pacientes_familiar(?, ?)");
                $query->execute([$id_familiar, $busqueda]);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function consultarSolicitudesFamiliar($id_familiar, $busqueda = '') {
            try {
                $query = $this->conn->prepare("call consultar_solicitudes_familiar(?, ?)");
                $query->execute([$id_familiar, $busqueda]);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    class cuidador extends usuario {

        public function consultarPacientesAsignados($id_cuidador, $busqueda = '') {
            try {
                $query = $this->conn->prepare("call consultar_pacientes_cuidador(?, ?)");
                $query->execute([$id_cuidador, $busqueda]);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    class administrador extends usuario{

        public function registrarEmpleado($datos) {
            try {
                $query = $this->conn->prepare("CALL registrar_empleado(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $query->bindParam(1,  $datos['tipo_documento']);
                $query->bindParam(2,  $datos['documento_identificacion']);
                $query->bindParam(3,  $datos['nombre']);
                $query->bindParam(4,  $datos['apellido']);
                $query->bindParam(5,  $datos['direccion']);
                $query->bindParam(6,  $datos['correo_electronico']);
                $query->bindParam(7,  $datos['contraseña']); // Contraseña hasheada desde el controlador
                $query->bindParam(8,  $datos['numero_telefono']);
                $query->bindParam(9,  $datos['fecha_contratacion']);
                $query->bindParam(10, $datos['tipo_contrato']);
                $query->bindParam(11, $datos['contacto_emergencia']);
                $query->bindParam(12, $datos['fecha_nacimiento']);
                $query->bindParam(13, $datos['nombre_rol']); // 'Administrador' o 'Cuidador'

                $query->execute();

                return $query->fetch(PDO::FETCH_ASSOC); // Retorna el id del nuevo usuario

            } catch (Exception $e) {
                // Si algo sale mal (como un correo duplicado) la excepción se lanza al controlador
                throw $e;
            }
        }

        public function consultaGlobal($filtro_rol, $busqueda, $id_admin_actual) {
            try {
                $query = $this->conn->prepare("call admin_consulta_global(?, ?, ?)");
                $query->execute([$filtro_rol, $busqueda, $id_admin_actual]);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function desactivarUsuario($id_usuario_a_desactivar, $id_admin_actual) {
            try {
                $query = $this->conn->prepare("call desactivar_usuario(?, ?)");
                $query->execute([$id_usuario_a_desactivar, $id_admin_actual]);
                return true;
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function desactivarPaciente($id_paciente) {
            try {
                $query = $this->conn->prepare("call desactivar_paciente(?)");
                $query->execute([$id_paciente]);
                return true;
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
?>