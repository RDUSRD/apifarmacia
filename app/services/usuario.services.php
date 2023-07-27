<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:3000");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type");
    http_response_code(200);
    exit();
}

require_once '../connection.php';

class UsuarioService
{
    // Función para crear un nuevo usuario
    function crearUsuario($usuario)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "INSERT INTO usuario (usu_Name, usu_Email, usu_Password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $usuario['name'], $usuario['email'], $usuario['password']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);
    }

    // Función para obtener un usuario por ID
    function obtenerUsuario($id)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "SELECT * FROM usuario WHERE usu_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $usuario = array('id' => $row['usu_id'], 'name' => $row['usu_Name'], 'email' => $row['usu_Email'], 'password' => $row['usu_Password']);
        mysqli_stmt_close($stmt);
        $db->close($db);
        return $usuario;
    }

    // Función para obtener todos los usuarios
    function obtenerTodosLosUsuarios()
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "SELECT * FROM usuario");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $usuarios = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $usuario = array('id' => $row['usu_id'], 'name' => $row['usu_Name'], 'email' => $row['usu_Email'], 'password' => $row['usu_Password']);
            array_push($usuarios, $usuario);
        }
        mysqli_stmt_close($stmt);
        $db->close($db);
        return $usuarios;
    }

    // Función para actualizar un usuario
    function actualizarUsuario($usuario)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "UPDATE usuario SET usu_Name = ?, usu_Email = ?, usu_Password = ? WHERE usu_id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $usuario['name'], $usuario['email'], $usuario['password'], $usuario['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);
    }

    // Función para eliminar un usuario
    function eliminarUsuario($id)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "DELETE FROM usuario WHERE usu_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);
    }

    // Función para verificar el inicio de sesión de un usuario
    function verificarInicioSesion($email, $password)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "SELECT * FROM usuario WHERE usu_Email = ? AND usu_Password = ?");
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);

        if (mysqli_num_rows($result) == 1) {
            // Iniciar sesión y devolver verdadero si se encuentra un usuario con las credenciales proporcionadas
            session_start();
            $_SESSION["email"] = $email;
            return true;
        } else {
            // Devolver falso si no se encuentra un usuario con las credenciales proporcionadas
            return false;
        }
    }
}

// Crear una instancia de la clase UsuarioService
$service = new UsuarioService();

// Obtener el método HTTP utilizado
$method = $_SERVER['REQUEST_METHOD'];

// Obtener el ID del usuario, si existe
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

// Procesar la solicitud
switch ($method) {
    case 'GET':
        // Obtener un usuario por ID
        if (isset($id)) {
            $usuario = $service->obtenerUsuario($id);
            echo json_encode($usuario);
        }
        // Obtener todos los usuarios
        else {
            $usuarios = $service->obtenerTodosLosUsuarios();
            echo json_encode($usuarios);
        }
        break;
    case 'POST':
        // Crear un nuevo usuario
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['name']) && isset($data['email']) && isset($data['password'])) {
            $usuario = array('name' => $data['name'], 'email' => $data['email'], 'password' => $data['password']);
            $service->crearUsuario($usuario);
            http_response_code(201);
            echo json_encode(array('message' => 'Usuario creado exitosamente.'));
        } else {
            http_response_code(400);
            echo json_encode(array('message' => 'Datos incompletos o en formato incorrecto.'));
        }
        break;
    case 'PUT':
        // Actualizar un usuario existente
        $data = json_decode(file_get_contents('php://input'), true);
        $usuario = array('id' => $data['id'], 'name' => $data['name'], 'email' => $data['email'], 'password' => $data['password']);
        $service->actualizarUsuario($usuario);
        http_response_code(200);
        echo json_encode(array('message' => 'Usuario actualizado exitosamente.'));
        break;
    case 'DELETE':
        // Eliminar un usuario existente
        $service->eliminarUsuario($id);
        http_response_code(200);
        echo json_encode(array('message' => 'Usuario eliminado exitosamente.'));
        break;
    default:
        // Método HTTP no válido
        http_response_code(405);
        echo json_encode(array('message' => 'Método HTTP no válido.'));
        break;
}
