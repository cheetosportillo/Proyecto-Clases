<?php
$nombre = $_POST['nombre'] ?? '';
$Descripcion = $_POST['Descripcion'] ?? '';
$idRuta = $_POST['idRuta'] ?? '';
$opcion = $_POST['opcion'] ?? '';

if ($opcion == "guardar") {
    require_once "../config/conexion.php";

    //$clave = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO ruta (nombre, Descripcion) VALUES(?,?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $nombre, $Descripcion);
    if ($stmt->execute()) {
        echo json_encode([
            "estado" => true,
            "mensaje" => "Datos Guardados Correctamente"
        ]);
    } else {
        echo json_encode([
            "estado" => false,
            "mensaje" => "Error al guardar los datos"
        ]);
    }
    $stmt->close();
    $conexion->close();
}
if ($opcion == "modificar") {
    require_once "../config/conexion.php";

    $sql = "UPDATE ruta set nombre=?, Descripcion=? WHERE idRuta=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssi",$nombre, $Descripcion, $idRuta);
    if ($stmt->execute()) {
        echo json_encode([
            "estado" => true,
            "mensaje" => "Datos Modificados Correctamente"
        ]);
    } else {
        echo json_encode([
            "estado" => false,
            "mensaje" => "Error al modificar los datos"
        ]);
    }
    $stmt->close();
    $conexion->close();
}
if ($opcion == "eliminar") {
    
    require_once "../config/conexion.php";

    $sql = "DELETE FROM ruta WHERE idRuta=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idRuta);
    if ($stmt->execute()) {
        echo json_encode([
            "estado" => true,
            "mensaje" => "Datos Eliminados Correctamente"
        ]);
    } else {
        echo json_encode([
            "estado" => false,
            "mensaje" => "Error al eliminar los datos"
        ]);
    }
    $stmt->close();
    $conexion->close();
}

if ($opcion == "consultar") {
    require_once "../config/conexion.php";

    try {
        $sql = "SELECT idRuta, nombre, Descripcion
                FROM ruta
                ORDER BY nombre";

        $resultado = $conexion->query($sql);

        if (!$resultado) {
            throw new Exception("Error al consultar las Rutas");
        }

        $ruta = [];

        while ($fila = $resultado->fetch_assoc()) {
            $ruta[] = [
                'idRuta'      => $fila['idRuta'],
                'nombre'      => $fila['nombre'],
                'Descripcion' => $fila['Descripcion']
            ];
        }

        echo json_encode($ruta, JSON_UNESCAPED_UNICODE);

        $resultado->free();
        $conexion->close();
    } catch (Throwable $e) {
        echo json_encode([
            "estado"  => false,
            "mensaje" => "No se pudieron obtener los registros: " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

if ($opcion == "consultarIndividual") {
    require_once "../config/conexion.php";

    $sql = "SELECT idRuta,nombre, Descripcion FROM ruta WHERE idRuta=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idRuta);

    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $datos = $resultado->fetch_assoc();
        echo json_encode([
            "estado" => true,
            "datos" => [
                "idRuta" => $datos["idRuta"],
                "nombre" => $datos["nombre"],
                "Descripcion" => $datos["Descripcion"],
            ]
        ]);
    } else {
        echo json_encode([
            "estado" => false,
            "datos" => "No Existe"
        ]);
    }

    $stmt->close();
    $conexion->close();
}
