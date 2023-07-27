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

class ProductoService
{
    // Función para crear un nuevo producto
    function crearProducto($producto)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "INSERT INTO producto (prod_name, prod_img) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $producto['name'], $producto['img']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);
    }

    // Función para obtener un producto por ID
    function obtenerProducto($id)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "SELECT * FROM producto WHERE prod_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $producto = array('id' => $row['prod_id'], 'name' => $row['prod_name'], 'img' => $row['prod_img']);
        mysqli_stmt_close($stmt);
        $db->close($db);
        return $producto;
    }

    // Función para obtener todos los productos
    function obtenerTodosLosProductos()
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "SELECT * FROM producto");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $productos = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $producto = array('id' => $row['prod_id'], 'name' => $row['prod_name'], 'img' => $row['prod_img']);
            array_push($productos, $producto);
        }
        mysqli_stmt_close($stmt);
        $db->close($db);
        return $productos;
    }

    // Función para actualizar un producto
    function actualizarProducto($producto)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "UPDATE producto SET prod_name = ?, prod_img = ? WHERE prod_id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $producto['name'], $producto['img'], $producto['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);
    }

    // Función para eliminar un producto
    function eliminarProducto($id)
    {
        $db = new dbconnect();
        $conn = $db->connect();
        $stmt = mysqli_prepare($conn, "DELETE FROM producto WHERE prod_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $db->close($db);
    }
}

// Crear una instancia de la clase ProductoService
$service = new ProductoService();

// Obtener el método HTTP utilizado
$method = $_SERVER['REQUEST_METHOD'];

// Obtener el ID del producto, si existe
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

// Procesar la solicitud
switch ($method) {
    case 'GET':
        // Obtener un producto por ID
        if (isset($id)) {
            $producto = $service->obtenerProducto($id);
            echo json_encode($producto);
        }
        // Obtener todos los productos
        else {
            $productos = $service->obtenerTodosLosProductos();
            echo json_encode($productos);
        }
        break;
    case 'POST':
        // Crear un nuevo producto
        $name = $_POST['name'];
        $img = $_FILES['image']['name'];
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = array("jpg", "jpeg", "png", "gif");
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $producto = array('name' => $name, 'img' => $targetFile);
                $service->crearProducto($producto);
                http_response_code(201);
                echo json_encode(array('message' => 'Producto creado exitosamente.'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Error al subir la imagen.'));
            }
        } else {
            http_response_code(400);
            echo json_encode(array('message' => 'Tipo de archivo no permitido.'));
        }
        break;
    case 'PUT':
        // Actualizar un producto existente
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['id']) && isset($data['name'])) {
            $producto = array('id' => $data['id'], 'name' => $data['name']);
            if (isset($_FILES['image'])) {
                // Eliminar la imagen anterior del servidor
                $productoAnterior = $service->obtenerProducto($data['id']);
                if ($productoAnterior && isset($productoAnterior['img'])) {
                    unlink($productoAnterior['img']);
                }
                // Subir la nueva imagen al servidor
                $targetDir = "uploads/";
                $targetFile = $targetDir . basename($_FILES["image"]["name"]);
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                $allowedTypes = array("jpg", "jpeg", "png", "gif");
                if (in_array($imageFileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                        // Guardar la ruta de la nueva imagen en la base de datos
                        $producto['img'] = $targetFile;
                    } else {
                        http_response_code(500);
                        echo json_encode(array('message' => 'Error al subir la imagen.'));
                        exit();
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array('message' => 'Tipo de archivo no permitido.'));
                    exit();
                }
            }
            $service->actualizarProducto($producto);
            http_response_code(200);
            echo json_encode(array('message' => 'Producto actualizado exitosamente.'));
        } else {
            http_response_code(400);
            echo json_encode(array('message' => 'Datos incompletos o en formato incorrecto.'));
        }
        break;
    case 'DELETE':
        // Eliminar un producto existente
        $service->eliminarProducto($id);
        http_response_code(200);
        echo json_encode(array('message' => 'Producto eliminado exitosamente.'));
        break;
    default:
        // Método HTTP no válido
        http_response_code(405);
        echo json_encode(array('message' => 'Método HTTP no válido.'));
        break;
}