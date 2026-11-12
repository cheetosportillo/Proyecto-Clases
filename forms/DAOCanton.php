<?php
$nombre = $_POST['nombre'] ?? '';
$id_canton = $_POST['id_canton'] ?? '';
$opcion = $_POST['opcion'] ?? '';

if ($opcion == "guardar") {
    require_once "../config/conexion.php";
    $sql = "INSERT INTO canton (nombre) VALUES(?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $nombre);
    if ($stmt->execute()) {
        echo json_encode(["estado" => true, "mensaje" => "Datos Guardados Correctamente"]);
    } else {
        echo json_encode(["estado" => false, "mensaje" => "Error al guardar los datos"]);
    }
    $stmt->close();
    $conexion->close();
}

if ($opcion == "modificar") {
    require_once "../config/conexion.php";
    $sql = "UPDATE canton SET nombre=? WHERE id_canton=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $nombre, $id_canton);
    if ($stmt->execute()) {
        echo json_encode(["estado" => true, "mensaje" => "Datos Modificados Correctamente"]);
    } else {
        echo json_encode(["estado" => false, "mensaje" => "Error al modificar los datos"]);
    }
    $stmt->close();
    $conexion->close();
}

if ($opcion == "eliminar") {
    require_once "../config/conexion.php";
    $sql = "DELETE FROM canton WHERE id_canton=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_canton);
    if ($stmt->execute()) {
        echo json_encode(["estado" => true, "mensaje" => "Datos Eliminados Correctamente"]);
    } else {
        echo json_encode(["estado" => false, "mensaje" => "Error al eliminar los datos"]);
    }
    $stmt->close();
    $conexion->close();
}

if ($opcion == "consultar") {
    require_once "../config/conexion.php";
    try {
        $sql = "SELECT id_canton, nombre FROM canton ORDER BY nombre";
        $resultado = $conexion->query($sql);
        if (!$resultado) throw new Exception("Error al consultar los cantones");

        $canton = [];
        while ($fila = $resultado->fetch_assoc()) {
            $canton[] = [
                'id_canton' => $fila['id_canton'],
                'nombre'    => $fila['nombre']
            ];
        }
        echo json_encode($canton, JSON_UNESCAPED_UNICODE);
        $resultado->free();
        $conexion->close();
    } catch (Throwable $e) {
        echo json_encode(["estado" => false, "mensaje" => "No se pudieron obtener los registros: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
}

if ($opcion == "consultarIndividual") {
    require_once "../config/conexion.php";
    $sql = "SELECT id_canton, nombre FROM canton WHERE id_canton=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_canton);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $datos = $resultado->fetch_assoc();
        echo json_encode(["estado" => true, "datos" => ["id_canton" => $datos["id_canton"], "nombre" => $datos["nombre"]]]);
    } else {
        echo json_encode(["estado" => false, "datos" => "No Existe"]);
    }
    $stmt->close();
    $conexion->close();
}
