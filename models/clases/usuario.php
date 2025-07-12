<?php

    // CLASE PRINCIPAL: usuario
    // Clase "madre" de la que heredarán (Familiar, Cuidador, Administrador).
    // Contiene las propiedades y métodos que todos los tipos de usuario comparten.
    class usuario{

        // Esta variable guardará la conexión a la base de datos.
        // Es 'protected' para que solo esta clase y sus clases hijas puedan usarla.
        protected $conn;

        // METODO CONSTRUCTOR
        // Esto se ejecuta automáticamente cada vez que se crea un objeto de esta clase ($Usuario = new usuario();).
        // La única función aquí es conectarse a la base de datos.
        public function __construct() {
            // Se trae el archivo que tiene la lógica para conectarse a la base de datos.
            // __DIR__ es una constante de PHP que significa "el directorio de este mismo archivo".
            // Esto hace que la ruta siempre funcione sin importar desde dónde se llame a la clase.
            include(__DIR__ . '/../data_base/database.php');
            
            // Se asigna la conexión ($conn) que viene del archivo 'database.php' a la propiedad de esta clase ($this->conn).
            // Cualquier método dentro de esta clase puede usar "$this->conn" para hablar con la base de datos.
            $this->conn = $conn;
        }

        // MÉTODO Login: manejo dinamico de roles
        // Este método se usa para verificar si un usuario puede iniciar sesión.
        public function Login($tipo_documento, $documento_identificacion) {
            try {
                // Prepara una consulta SQL con la propiedad "$this->conn" que tiene la conexion a BD para buscar un usuario.
                // Usar 'prepare' es una medida de seguridad clave para prevenir ataques de inyección SQL.
                $validar = $this->conn->prepare("
                    select u.*, r.nombre_rol 
                    from tb_usuario u 
                    inner join tb_rol r on u.id_rol = r.id_rol
                    where u.tipo_documento = ? 
                    and u.documento_identificacion = ? 
                    and u.estado = 'Activo'
                ");
                // Ejecuta la consulta que se preparo pasando los datos de forma segura.
                // El "?" en la consulta se reemplaza por los valores que se pasan aquí.
                $validar->execute([$tipo_documento, $documento_identificacion]);
                
                // Devuelve todos los resultados que encontró en un formato de array.
                // Si no encuentra nada devuelve un array vacío.
                return $validar->fetchAll(PDO::FETCH_ASSOC); // formato asociativo

            } catch (Exception $e) {
                // Si algo sale mal (ej: la base de datos se cae) se "lanza" el error.
                // Esto permite que el archivo que llamó a este método (el controlador) se encargue del problema.
                throw $e;
            }
        }

        // MÉTODO Actualizar
        // Llama a un procedimiento almacenado en la base de datos para actualizar la información de un usuario.
        public function Actualizar($datos) {
            try {
                // Prepara la llamada al procedimiento almacenado 'actualizar_usuario'.
                $actualizar = $this->conn->prepare("call actualizar_usuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"); // "?" son marcadores

                // Vincula cada '?' del procedimiento con una variable del array '$datos'.
                // Esto se hace para pasar los datos de forma segura y en el orden correcto.
                $actualizar->bindParam(1,  $datos['id_usuario']);
                $actualizar->bindParam(2,  $datos['tipo_documento']);
                $actualizar->bindParam(3,  $datos['documento_identificacion']);
                $actualizar->bindParam(4,  $datos['nombre']);
                $actualizar->bindParam(5,  $datos['apellido']);
                $actualizar->bindParam(6,  $datos['direccion']);
                $actualizar->bindParam(7,  $datos['correo_electronico']);
                $actualizar->bindParam(8,  $datos['numero_telefono']);
                $actualizar->bindParam(9,  $datos['fecha_contratacion']);
                $actualizar->bindParam(10, $datos['tipo_contrato']);
                $actualizar->bindParam(11, $datos['contacto_emergencia']);
                $actualizar->bindParam(12, $datos['fecha_nacimiento']);
                $actualizar->bindParam(13, $datos['parentesco']);
                $actualizar->bindParam(14, $datos['nombre_rol']); 

                // Ejecuta el procedimiento con los datos que se pasaron.
                $actualizar->execute();

                // Si todo sale bien devuelve 'true'.
                return true;

            } catch (Exception $e) {
                // Si hay un error lo lanza para que el controlador lo maneje.
                throw $e;
            }
        }

        // MÉTODO obtenerPorId
        // Busca y devuelve toda la información de un único usuario usando su ID.
        public function obtenerPorId($id_usuario) {
            try {
                // Prepara la consulta para seleccionar un usuario por su ID.
                // Se usan 'left join' para traer también el número de teléfono y el rol.
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
                // Ejecuta la consulta pasando el ID del usuario.
                $stmt->execute([$id_usuario]);
                // Devuelve solo una fila de resultado que son los datos del usuario encontrado.
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                // Lanza el error si algo falla.
                throw $e;
            }
        }
        
        // MÉTODO obtenerUsuariosPorRol
        // Busca y devuelve una lista de todos los usuarios que tienen un rol específico (ej: todos los "Familiares").
        public function obtenerUsuariosPorRol($nombre_rol) {
            try {
                // Prepara la consulta para buscar usuarios por el nombre de su rol.
                $query = $this->conn->prepare("
                    SELECT u.id_usuario, u.nombre, u.apellido 
                    FROM tb_usuario u
                    JOIN tb_rol r ON u.id_rol = r.id_rol
                    WHERE r.nombre_rol = ? AND u.estado = 'Activo'
                    ORDER BY u.apellido, u.nombre
                ");
                // Ejecuta la consulta pasando el rol que se busca.
                $query->execute([$nombre_rol]);
                // Devuelve todos los usuarios encontrados.
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                // Si hay un error lo registra en un log de errores del servidor y devuelve un array vacío.
                error_log("Error al obtener usuarios por rol: " . $e->getMessage());
                return [];
            }
        }
    }

    // CLASE HIJA: familiar
    // Hereda todo lo de la clase "usuario" (la conexión, los métodos, etc.)
    // y tiene sus propios métodos exclusivos para el familiar.
    class familiar extends usuario {

        // MÉTODO registrar
        // Llama a un procedimiento almacenado para registrar a un nuevo familiar.
        public function registrar($datos) {
            try {
                // Prepara la llamada al procedimiento 'registrar_familiar'.
                $registrar = $this->conn->prepare("call registrar_familiar(?, ?, ?, ?, ?, ?, ?, ?, ?)");

                // Vincula cada '?' con los datos del formulario.
                $registrar->bindParam(1,  $datos['tipo_documento']);
                $registrar->bindParam(2,  $datos['documento_identificacion']);
                $registrar->bindParam(3,  $datos['nombre']);
                $registrar->bindParam(4,  $datos['apellido']);
                $registrar->bindParam(5,  $datos['direccion']);
                $registrar->bindParam(6,  $datos['correo_electronico']);
                $registrar->bindParam(7,  $datos['contraseña']); // Esta contraseña ya viene procesada (hashed) desde el controlador.
                $registrar->bindParam(8,  $datos['numero_telefono']);
                $registrar->bindParam(9,  $datos['parentesco']);

                // Ejecuta el procedimiento.
                $registrar->execute();

                // Devuelve el resultado del procedimiento (el ID del usuario creado).
                return $registrar->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                throw $e;
            }
        }

        // MÉTODO consultarPacientesFamiliar
        // Busca todos los pacientes que están asociados a un familiar específico.
        public function consultarPacientesFamiliar($id_familiar, $busqueda = '') {
            try {
                $query = $this->conn->prepare("call consultar_pacientes_familiar(?, ?)");
                $query->execute([$id_familiar, $busqueda]);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    // CLASE HIJA: cuidador
    // Hereda todo lo de "usuario" y añade métodos específicos para el cuidador.
    class cuidador extends usuario {

        // MÉTODO consultarPacientesAsignados 
        // Busca todos los pacientes que un cuidador específico tiene a su cargo.
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

    // CLASE HIJA: administrador
    // Hereda todo lo de "usuario" y añade métodos para el admin.
    class administrador extends usuario{

        // MÉTODO registrarEmpleado
        // Registra un nuevo empleado (puede ser otro Administrador o un Cuidador).
        public function registrarEmpleado($datos) {
            try {
                $registrarEmp = $this->conn->prepare("call registrar_empleado(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                // Vincula los datos del empleado.
                $registrarEmp->bindParam(1,  $datos['tipo_documento']);
                $registrarEmp->bindParam(2,  $datos['documento_identificacion']);
                $registrarEmp->bindParam(3,  $datos['nombre']);
                $registrarEmp->bindParam(4,  $datos['apellido']);
                $registrarEmp->bindParam(5,  $datos['direccion']);
                $registrarEmp->bindParam(6,  $datos['correo_electronico']);
                $registrarEmp->bindParam(7,  $datos['contraseña']); // Esta contraseña ya viene procesada (hashed) desde el controlador.
                $registrarEmp->bindParam(8,  $datos['numero_telefono']);
                $registrarEmp->bindParam(9,  $datos['fecha_contratacion']);
                $registrarEmp->bindParam(10, $datos['tipo_contrato']);
                $registrarEmp->bindParam(11, $datos['contacto_emergencia']);
                $registrarEmp->bindParam(12, $datos['fecha_nacimiento']);
                $registrarEmp->bindParam(13, $datos['nombre_rol']);

                $registrarEmp->execute();

                // Devuelve el ID del nuevo empleado creado.
                return $registrarEmp->fetch(PDO::FETCH_ASSOC);

            } catch (Exception $e) {
                // Si algo sale mal (ej: correo duplicado), la excepción se lanza para que el controlador la maneje.
                throw $e;
            }
        }

        // MÉTODO consultaGlobal 
        // Es un buscador universal para el administrador. Puede buscar pacientes o usuarios por rol.
        public function consultaGlobal($filtro_rol, $busqueda, $id_admin_actual) {
            try {
                $query = $this->conn->prepare("call admin_consulta_global(?, ?, ?)");
                $query->execute([$filtro_rol, $busqueda, $id_admin_actual]);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                throw $e;
            }
        }

        // MÉTODO desactivarUsuario 
        // Cambia el estado de un usuario a 'Inactivo'.
        public function desactivarUsuario($id_usuario_a_desactivar, $id_admin_actual) {
            try {
                $query = $this->conn->prepare("call desactivar_usuario(?, ?)");
                $query->execute([$id_usuario_a_desactivar, $id_admin_actual]);
                return true;
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
?>