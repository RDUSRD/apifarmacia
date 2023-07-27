<?php
class dbconnect
{

    private $servername = "localhost";
    private $username = "root";
    private $password = "";

    function connect()
    {
        $conn = mysqli_connect($this->servername, $this->username, $this->password);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        try {
            // Verificar si la base de datos existe
            $result = mysqli_query($conn, "SHOW DATABASES");
            $databaseExists = false;
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['Database'] == "farmaciasaas") {
                    $databaseExists = true;
                    break;
                }
            }

            if (!$databaseExists) { 
                // Crear base de datos
                $sql = "CREATE DATABASE farmaciasaas";
                if (mysqli_query($conn, $sql)) {
                    $databaseExists = true;
                } else {
                    echo "Error al crear la base de datos: " . mysqli_error($conn);
                }
            }

            if ($databaseExists) {
                // Seleccionar la base de datos
                mysqli_select_db($conn, "farmaciasaas");

                // Crear tabla Usuario
                $cons1 = "CREATE TABLE usuario (
            usu_id INT(6) AUTO_INCREMENT PRIMARY KEY,
            usu_Name VARCHAR(30) NOT NULL,
            usu_Email VARCHAR(30) NOT NULL,
            usu_Password VARCHAR(50),
            usu_fechaIngreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
                // Crear tabla Producto
                $cons2 = "CREATE TABLE producto (
            prod_id INT(6) AUTO_INCREMENT PRIMARY KEY,
            prod_name VARCHAR(30) NOT NULL,
            prod_img VARCHAR(45) NOT NULL,
            prod_fechaIngreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
                // Crear tabla Proveedor
                $cons3 = "CREATE TABLE proveedor (
            prov_id INT(6) AUTO_INCREMENT PRIMARY KEY,
            prov_Name VARCHAR(30) NOT NULL
            )";

                // Crear tabla Inventario
                $cons6 = "CREATE TABLE inventario (
            inv_id INT(6) AUTO_INCREMENT PRIMARY KEY,
            inv_prod_id INT(6) NOT NULL,
            inv_clas INT(6) NOT NULL,
            inv_cantidad VARCHAR(45) NOT NULL,
            inv_fechaVencimiento VARCHAR(10) NOT NULL,
            inv_NroLote VARCHAR(10) NOT NULL,
            inv_prov_Id INT(6) NOT NULL,
            inv_farm_ INT(6) NOT NULL,
            inv_usu_Id INT(6) NOT NULL,
            inv_fechaIngreso VARCHAR(20) NOT NULL,
            inv_NroFactura VARCHAR(10) NOT NULL,
            FOREIGN KEY (inv_prod_Id) REFERENCES producto(prod_id),
            FOREIGN KEY (inv_prov_Id) REFERENCES proveedor(prov_id),
            FOREIGN KEY (inv_usu_Id) REFERENCES usuario(usu_id)
            )";

                // Guardando querys
                $Tables = array($cons1, $cons2, $cons3, $cons6);

                // Ejecutando tablas
                foreach ($Tables as &$Table) {
                    $sql = $Table;
                    if (mysqli_query($conn, $sql)) {
                    } else {
                        echo "Error al crear la tabla: " . mysqli_error($conn);
                    }
                }
            }

        } catch (Exception $e) {
            // echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }

        return $conn;
    }

    function close($conn)
    {
        mysqli_close($conn->connect());
    }
}
