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

require_once "usuario.services.php";



// Verificar el inicio de sesión del usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $data = json_decode(file_get_contents("php://input"), true);
  $email = $data["email"];
  $password = $data["password"];

  $service = new UsuarioService();
  $success = $service->verificarInicioSesion($email, $password);

  if ($success) {
    // Devolver una respuesta exitosa si se encuentra un usuario con las credenciales proporcionadas
    http_response_code(200);
    echo json_encode(array("success" => true));
  } else {
    // Devolver una respuesta de error si no se encuentra un usuario con las credenciales proporcionadas
    http_response_code(401);
    echo json_encode(array("success" => false));
  }
}
?>