<?php
require_once 'conexion.php';

$conexion = new Conexion();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Consulta para obtener un estudiante por ID 
    if (isset($_GET['id'])) {
        $sql = $conexion->prepare("SELECT * FROM estudiantes WHERE id=:id");
        $sql->bindValue(':id', $_GET['id']);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;
    }

    // Consulta para obtener un estudiante por Nombre 
    if (isset($_GET['nombre'])) {

        $sql = $conexion->prepare("SELECT * FROM estudiantes WHERE nombres LIKE :nombre");
        $busqueda = "%" . $_GET['nombre'] . "%";
        $sql->bindValue(':nombre', $busqueda);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);

        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;
    }
    // Consulta para obtener todos los estudiantes
    $sql = $conexion->prepare("SELECT * FROM estudiantes");

    $sql->execute();
    $sql->setFetchMode(PDO::FETCH_ASSOC);
    header("HTTP/1.1 200 OK");
    echo json_encode($sql->fetchAll());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $json = file_get_contents('php://input');
    
    $datos = json_decode($json, true);

    $nombres = $datos['nombres'];
    $apellidos = $datos['apellidos'];
    $dni = $datos['dni'];

    $sql = "INSERT INTO estudiantes (nombres, apellidos, dni) VALUES (:nombres, :apellidos, :dni)";
    $stmt = $conexion->prepare($sql);
    
    $stmt->bindParam(':nombres', $nombres);
    $stmt->bindParam(':apellidos', $apellidos);
    $stmt->bindParam(':dni', $dni);
    
    $stmt->execute();

    header("HTTP/1.1 201 Created");
    echo json_encode(["mensaje" => "Estudiante creado con éxito"]);
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'PUT'){
        // Obtener los datos enviados mediante la solicitud PUT
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Verificar si JSON es válido
        if ($data === null) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("error" => "JSON inválido"));
            exit;
        }
        
        // Verificar si se proporciona un ID de estudiante y al menos el campo 'nombres'
        if (!empty($data['id']) && !empty($data['nombres'])) {
            $id = $data['id'];
            $nombres = $data['nombres'];
        
            // Construir la consulta SQL para actualizar el nombre del estudiante
            $sql = "UPDATE estudiantes SET nombres = :nombres WHERE id = :id";
        
            // Preparar la consulta
            $stmt = $conexion->prepare($sql);
        
            // Vincular los valores y ejecutar la consulta
            $stmt->bindParam(':nombres', $nombres);
            $stmt->bindParam(':id', $id);
        
            // Ejecutar la consulta
            try {
                $stmt->execute();
                header("HTTP/1.1 200 OK");
                echo json_encode(array("mensaje" => "Actualización exitosa"));
            } catch (PDOException $e) {
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode(array("error" => "Error al actualizar: " . $e->getMessage()));
            }
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("error" => "Se requiere un ID de estudiante y el campo 'nombres'"));
        }
        exit;
    }

    if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (isset($data['id'])) {
            $sql = "DELETE FROM estudiantes WHERE id=:id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':id', $data['id']);
            
            try {
                $stmt->execute();
                header("HTTP/1.1 200 OK");
                echo json_encode(array("mensaje" => "Estudiante eliminado con éxito"));
            } catch (PDOException $e) {
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode(array("error" => "Error al eliminar: " . $e->getMessage()));
            }
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("error" => "Se requiere un ID de estudiante"));
        }
        exit;
    }
    
?>