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

class InventarioService
{
    // Función para crear un nuevo producto en el inventario
    function crearProductoEnInventario($producto)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "INSERT INTO inventario (inv_prod_id, inv_clas_Id, inv_cantidad, inv_fechaVencimiento, inv_NroLote, inv_prov_Id, inv_farm_Id, inv_usu_Id, inv_fechaIngreso, inv_NroFactura) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iisssiiiss", $producto['prod_id'], $producto['clas_Id'], $producto['cantidad'], $producto['fechaVencimiento'], $producto['NroLote'], $producto['prov_Id'], $producto['farm_Id'], $producto['usu_Id'], $producto['fechaIngreso'], $producto['NroFactura']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);
    }

    // Función para obtener un producto del inventario por ID
    function obtenerProductoEnInventario($id)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "SELECT * FROM inventario WHERE inv_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $producto = array('id' => $row['inv_id'], 'prod_id' => $row['inv_prod_id'], 'clas_Id' => $row['inv_clas_Id'], 'cantidad' => $row['inv_cantidad'], 'fechaVencimiento' => $row['inv_fechaVencimiento'], 'NroLote' => $row['inv_NroLote'], 'prov_Id' => $row['inv_prov_Id'], 'farm_Id' => $row['inv_farm_Id'], 'usu_Id' => $row['inv_usu_Id'], 'fechaIngreso' => $row['inv_fechaIngreso'], 'NroFactura' => $row['inv_NroFactura']);
        mysqli_stmt_close($stmt);
        $db->close($db);
        return $producto;
    }

    // Función para obtener todos los productos del inventario
    function obtenerTodosLosProductosEnInventario()
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "SELECT * FROM inventario");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $productos = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $producto = array('id' => $row['inv_id'], 'prod_id' => $row['inv_prod_id'], 'clas_Id' => $row['inv_clas_Id'], 'cantidad' => $row['inv_cantidad'], 'fechaVencimiento' => $row['inv_fechaVencimiento'], 'NroLote' => $row['inv_NroLote'], 'prov_Id' => $row['inv_prov_Id'], 'farm_Id' => $row['inv_farm_Id'], 'usu_Id' => $row['inv_usu_Id'], 'fechaIngreso' => $row['inv_fechaIngreso'], 'NroFactura' => $row['inv_NroFactura']);
            array_push($productos, $producto);
        }
        mysqli_stmt_close($stmt);
        $db->close($db);
        return $productos;
    }

    // Función para actualizar un producto en el inventario
    function actualizarProductoEnInventario($producto)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "UPDATE inventario SET inv_prod_id = ?, inv_clas_Id = ?, inv_cantidad = ?, inv_fechaVencimiento = ?, inv_NroLote = ?, inv_prov_Id = ?, inv_farm_Id = ?, inv_usu_Id = ?, inv_fechaIngreso = ?, inv_NroFactura = ? WHERE inv_id = ?");
        mysqli_stmt_bind_param($stmt, "iisssiiissi", $producto['prod_id'], $producto['clas_Id'], $producto['cantidad'], $producto['fechaVencimiento'], $producto['NroLote'], $producto['prov_Id'], $producto['farm_Id'], $producto['usu_Id'], $producto['fechaIngreso'], $producto['NroFactura'], $producto['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);
    }

    // Función para eliminar un producto del inventario
    function eliminarProductoEnInventario($id)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "DELETE FROM inventario WHERE inv_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);
    }
}

// Crear una instancia de la clase InventarioService
$service = new InventarioService();

// Obtener el método HTTP utilizado
$method = $_SERVER['REQUEST_METHOD'];

// Obtener el ID del producto, si existe
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

// Procesar la solicitud
switch ($method) {
    case 'GET':
        // Obtener todos los productos del inventario
        if (!isset($id)) {
            $productos = $service->obtenerTodosLosProductosEnInventario();
            echo json_encode($productos);
        }
        // Obtener un producto del inventario por ID
        else {
            $producto = $service->obtenerProductoEnInventario($id);
            echo json_encode($producto);
        }
        break;
    case 'POST':
        // Crear un nuevo producto en el inventario
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['prod_id']) && isset($data['clas_Id']) && isset($data['cantidad']) && isset($data['fechaVencimiento']) && isset($data['NroLote']) && isset($data['prov_Id']) && isset($data['farm_Id']) && isset($data['usu_Id']) && isset($data['fechaIngreso']) && isset($data['NroFactura'])) {
            $producto = array('prod_id' => $data['prod_id'], 'clas_Id' => $data['clas_Id'], 'cantidad' => $data['cantidad'], 'fechaVencimiento' => $data['fechaVencimiento'], 'NroLote' => $data['NroLote'], 'prov_Id' => $data['prov_Id'], 'farm_Id' => $data['farm_Id'], 'usu_Id' => $data['usu_Id'], 'fechaIngreso' => $data['fechaIngreso'], 'NroFactura' => $data['NroFactura']);
            $service->crearProductoEnInventario($producto);
            http_response_code(201);
            echo json_encode(array('message' => 'Producto creado exitosamente.'));
        } else {
            http_response_code(400);
            echo json_encode(array('message' => 'Datos incompletos o en formato incorrecto.'));
        }
        break;
    case 'PUT':
        // Actualizar un producto existente en el inventario
        $data = json_decode(file_get_contents('php://input'), true);
        $producto = array('id' => $data['id'], 'prod_id' => $data['prod_id'], 'clas_Id' => $data['clas_Id'], 'cantidad' => $data['cantidad'], 'fechaVencimiento' => $data['fechaVencimiento'], 'NroLote' => $data['NroLote'], 'prov_Id' => $data['prov_Id'], 'farm_Id' => $data['farm_Id'], 'usu_Id' => $data['usu_Id'], 'fechaIngreso' => $data['fechaIngreso'], 'NroFactura' => $data['NroFactura']);
        $service->actualizarProductoEnInventario($producto);
        http_response_code(200);
        echo json_encode(array('message' => 'Producto actualizado exitosamente.'));
        break;
    case 'DELETE':
        // Eliminar un producto existente en el inventario
        $service->eliminarProductoEnInventario($id);
        http_response_code(200);
        echo json_encode(array('message' => 'Producto eliminado exitosamente.'));
        break;
    default:
        // Método HTTP no válido
        http_response_code(405);
        echo json_encode(array('message' => 'Método HTTP no válido.'));
        break;
}