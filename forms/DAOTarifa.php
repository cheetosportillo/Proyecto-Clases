<?php
$desde     = $_POST['desde'] ?? '';
$hasta     = $_POST['hasta'] ?? '';
$tipo      = $_POST['tipo'] ?? '';
$precio    = $_POST['precio'] ?? '';
$id_tarifa = $_POST['id_tarifa'] ?? '';
$opcion    = $_POST['opcion'] ?? '';

if ($opcion == "guardar") {
    require_once "../Config/Conexion.php";
    $sql = "INSERT INTO tarifa (desde, hasta, tipo, precio) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ddsd", $desde, $hasta, $tipo, $precio);

    if ($stmt->execute()) {
        echo json_encode(["estado" => true, "mensaje" => "Datos Guardados Correctamente"]);
    } else {
        echo json_encode(["estado" => false, "mensaje" => "Error al guardar los datos"]);
    }
    $stmt->close();
    $conexion->close();
}

if ($opcion == "modificar") {
    require_once "../Config/Conexion.php";
    $sql = "UPDATE tarifa SET desde=?, hasta=?, tipo=?, precio=? WHERE id_tarifa=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ddsdi", $desde, $hasta, $tipo, $precio, $id_tarifa);

    if ($stmt->execute()) {
        echo json_encode(["estado" => true, "mensaje" => "Datos Modificados Correctamente"]);
    } else {
        echo json_encode(["estado" => false, "mensaje" => "Error al modificar los datos"]);
    }
    $stmt->close();
    $conexion->close();
}

if ($opcion == "eliminar") {
    require_once "../Config/Conexion.php";
    $sql = "DELETE FROM tarifa WHERE id_tarifa=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_tarifa);

    if ($stmt->execute()) {
        echo json_encode(["estado" => true, "mensaje" => "Datos Eliminados Correctamente"]);
    } else {
        echo json_encode(["estado" => false, "mensaje" => "Error al eliminar los datos"]);
    }
    $stmt->close();
    $conexion->close();
}

if ($opcion == "consultar") {
    require_once "../Config/Conexion.php";
    try {
        $sql = "SELECT id_tarifa, desde, hasta, tipo, precio FROM tarifa ORDER BY desde ASC";
        $resultado = $conexion->query($sql);
        $tarifas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $tarifas[] = $fila;
        }
        echo json_encode($tarifas, JSON_UNESCAPED_UNICODE);
        $resultado->free();
        $conexion->close();
    } catch (Throwable $e) {
        echo json_encode(["estado" => false, "mensaje" => "Error: " . $e->getMessage()]);
    }
}

if ($opcion == "consultarIndividual") {
    require_once "../Config/Conexion.php";
    $sql = "SELECT id_tarifa, desde, hasta, tipo, precio FROM tarifa WHERE id_tarifa=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_tarifa);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo json_encode(["estado" => true, "datos" => $resultado->fetch_assoc()]);
    } else {
        echo json_encode(["estado" => false, "datos" => "No Existe"]);
    }
    $stmt->close();
    $conexion->close();
}