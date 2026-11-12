<?php
$nombre = $_POST['nombre'] ?? '';
$id_cargo = $_POST['id_cargo'] ?? '';
$opcion = $_POST['opcion'] ?? '';

if ($opcion == "guardar") {
    require_once "../config/conexion.php";
    $sql = "INSERT INTO cargo (nombre) VALUES(?)";
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
    $sql = "UPDATE cargo SET nombre=? WHERE id_cargo=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $nombre, $id_cargo);
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
    $sql = "DELETE FROM cargo WHERE id_cargo=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_cargo);
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
        $sql = "SELECT id_cargo, nombre FROM cargo ORDER BY nombre";
        $resultado = $conexion->query($sql);
        if (!$resultado) throw new Exception("Error al consultar los cargos");

        $cargo = [];
        while ($fila = $resultado->fetch_assoc()) {
            $cargo[] = [
                'id_cargo' => $fila['id_cargo'],
                'nombre'   => $fila['nombre']
            ];
        }
        echo json_encode($cargo, JSON_UNESCAPED_UNICODE);
        $resultado->free();
        $conexion->close();
    } catch (Throwable $e) {
        echo json_encode(["estado" => false, "mensaje" => "No se pudieron obtener los registros: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
}

if ($opcion == "consultarIndividual") {
    require_once "../config/conexion.php";
    $sql = "SELECT id_cargo, nombre FROM cargo WHERE id_cargo=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_cargo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $datos = $resultado->fetch_assoc();
        echo json_encode(["estado" => true, "datos" => ["id_cargo" => $datos["id_cargo"], "nombre" => $datos["nombre"]]]);
    } else {
        echo json_encode(["estado" => false, "datos" => "No Existe"]);
    }
    $stmt->close();
    $conexion->close();
}
