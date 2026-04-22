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
?>