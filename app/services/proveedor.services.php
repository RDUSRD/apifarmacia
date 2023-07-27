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

class ProveedorService
{
    // Función para crear un nuevo proveedor
    function crearProveedor($proveedor)
    {
        $db = new DbConnect();
        $conn = $db->connect(); 
        $stmt = mysqli_prepare($conn, "INSERT INTO proveedor (prov_Name) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $proveedor['prov_Name']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);
    }

    // Función para obtener un proveedor por ID
    function obtenerProveedor($id)
    {
        $db = new DbConnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "SELECT * FROM proveedor WHERE prov_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $proveedor = array('id' => $row['prov_id'], 'name' => $row['prov_Name']);
        mysqli_stmt_close($stmt);
        $db->close($db);
        return $proveedor;
    }

    // Función para obtener todos los proveedores
    function obtenerTodosLosProveedores()
    {
        $db = new DbConnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "SELECT * FROM proveedor");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $proveedores = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $proveedor = array('id' => $row['prov_id'], 'name' => $row['prov_Name']);
            array_push($proveedores, $proveedor);
        }
        mysqli_stmt_close($stmt);
        $db->close($db);
        return $proveedores;
    }

    // Función para actualizar un proveedor
    function actualizarProveedor($proveedor)
    {
        $db = new DbConnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "UPDATE proveedor SET prov_Name = ? WHERE prov_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $proveedor['prov_Name'], $proveedor['prov_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);
    }

    // Función para eliminar un proveedor
    function eliminarProveedor($id)
    {
        $db = new DbConnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "DELETE FROM proveedor WHERE prov_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);
    }
}

// Crear una instancia de la clase ProveedorService
$service = new ProveedorService();

// Obtener el método HTTP utilizado
$method = $_SERVER['REQUEST_METHOD'];

// Obtener el ID del proveedor, si existe
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

// Procesar la solicitud
switch ($method) {
    case 'GET':
        // Obtener un proveedor por ID
        if (isset($id)) {
            $proveedor = $service->obtenerProveedor($id);
            echo json_encode($proveedor);
        }
        // Obtener todos los proveedores
        else {
            $proveedores = $service->obtenerTodosLosProveedores();
            echo json_encode($proveedores);
        }
        break;
    case 'POST':
        // Crear un nuevo proveedor
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['name'])) {
            $proveedor = array('prov_Name' => $data['name']);
            $service->crearProveedor($proveedor);
            http_response_code(201);
            echo json_encode(array('message' => 'Proveedor creado exitosamente.'));
        } else {
            http_response_code(400);
            echo json_encode(array('message' => 'Datos incompletos o en formato incorrecto.'));
        }
        break;
    case 'PUT':
        // Actualizar un proveedor existente
        $data = json_decode(file_get_contents('php://input'), true);
        $proveedor = array('prov_id' => $data['prov_id'], 'prov_Name' => $data['prov_Name']);
        $service->actualizarProveedor($proveedor);
        http_response_code(200);
        echo json_encode(array('message' => 'Proveedor actualizado exitosamente.'));
        break;
    case 'DELETE':
        // Eliminar un proveedor existente
        $service->eliminarProveedor($id);
        http_response_code(200);
        echo json_encode(array('message' => 'Proveedor eliminado exitosamente.'));
        break;
    default:
        // Método HTTP no válido
        http_response_code(405);
        echo json_encode(array('message' => 'Método HTTP no válido.'));
        break;
}