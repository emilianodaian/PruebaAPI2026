<?php
    require_once 'conexion.php';

    $conexion = new Conexion();

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = $conexion->prepare("SELECT * FROM estudiantes");
            
                $sql->execute();
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                header("HTTP/1.1 200 OK");
                echo json_encode($sql->fetchAll());
                exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $correo = $_POST['correo'];

        $sql = "INSERT INTO estudiantes (nombre, apellido, correo) VALUES (:nombre, :apellido, :correo)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        header('Location: index.php');
        exit;
    }
?>