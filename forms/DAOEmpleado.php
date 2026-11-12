<?php
$dui = $_POST['dui'] ?? '';
$nombre = isset($_REQUEST["nombre"]) ? $_REQUEST["nombre"] : "";
$apellido = isset($_REQUEST["apellido"]) ? $_REQUEST["apellido"] : "";
$telefono = $_POST['telefono'] ?? '';
$idempleado = $_POST['idempleado'] ?? '';

$opcion = isset($_REQUEST["opcion"]) ? $_REQUEST["opcion"] : "";

if ($opcion == "guardar") {
    require_once "../config/conexion.php";

    //$clave = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO empleado (dui, nombre, apellido, telefono) VALUES(?,?,?,?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $dui, $nombre, $apellido, $telefono);
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

    $sql = "UPDATE empleado set dui=?, nombre=?, apellido=?, telefono=? WHERE idempleado=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssi", $dui, $nombre, $apellido, $telefono, $idempleado);
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

    $sql = "DELETE FROM empleado WHERE idempleado=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idempleado);
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

if ($opcion == "consultarDUI") {
    require_once "../config/conexion.php";

    $sql = "SELECT dui FROM empleado WHERE dui=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $dui);

    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        echo json_encode([
            "estado" => true,
            "mensaje" => "Este Dui ya existe"
        ]);
    } else {
        echo json_encode([
            "estado" => false,
            "mensaje" => "a"
        ]);
    }

    $stmt->close();
    $conexion->close();
}

if ($opcion == "consultar") {
    require_once "../config/conexion.php";

    try {
        $sql = "SELECT idempleado,dui, nombre, apellido, telefono, concat(nombre,'-',apellido) as nombreCompleto
                FROM empleado
                ORDER BY apellido, nombre";

        $resultado = $conexion->query($sql);

        if (!$resultado) {
            throw new Exception("Error al consultar los empleadosxx");
        }

        $empleados = [];

        while ($fila = $resultado->fetch_assoc()) {
            //$empleados[] = $fila;
            $empleados[] = [
                'idempleado' => $fila['idempleado'],
                'dui' => $fila['dui'],
                'nombre' => $fila['nombre'],
                'apellido' => $fila['apellido'],
                'telefono' => $fila['telefono'],
                'nombreCompleto' => $fila['nombreCompleto'],
                'tamanio' => strlen($fila['nombre']) * strlen($fila['apellido'])
            ];
        }

        echo json_encode(
            $empleados,
            JSON_UNESCAPED_UNICODE
        );

        $resultado->free();
        $conexion->close();
    } catch (Throwable $e) {

        echo json_encode([
            "mensaje" => "No se pudieron obtener los registros"
        ], JSON_UNESCAPED_UNICODE);
    }
}
if ($opcion == "consultarIndividual") {
    require_once "../config/conexion.php";

    $sql = "SELECT idempleado, dui, nombre, apellido, telefono FROM empleado WHERE idempleado=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idempleado);

    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $datos = $resultado->fetch_assoc();
        echo json_encode([
            "estado" => true,
            "datos" => [
                "idempleado" => $datos["idempleado"],
                "dui" => $datos["dui"],
                "nombre" => $datos["nombre"],
                "apellido" => $datos["apellido"],
                "telefono" => $datos["telefono"]
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
