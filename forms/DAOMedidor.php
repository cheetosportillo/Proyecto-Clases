<?php
$canton     = $_POST['canton'] ?? '';
$ruta       = $_POST['ruta'] ?? '';
$direccion  = $_POST['direccion'] ?? '';
$referencia = $_POST['referencia'] ?? '';
$nmedidor   = $_POST['nmedidor'] ?? '';
$inicio     = $_POST['inicio'] ?? '';
$estado     = $_POST['estado'] ?? '';

$idcontrato = $_POST['idcontrato'] ?? '';
$idmedidor  = $_POST['idmedidor'] ?? '';
$opcion     = $_POST['opcion'] ?? '';

if ($opcion == "guardar") {
    require_once "../Config/Conexion.php";

    $sql = "INSERT INTO medidor (idcontrato, nmedidor, lectura_inicial, idcanton, idruta, direccion, referencia, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("isiiisss", $idcontrato, $nmedidor, $inicio, $canton, $ruta, $direccion, $referencia, $estado);

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

    $sql = "UPDATE medidor SET nmedidor=?, lectura_inicial=?, idcanton=?, idruta=?, direccion=?, referencia=?, estado=? 
            WHERE idmedidor=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("siiisssi", $nmedidor, $inicio, $canton, $ruta, $direccion, $referencia, $estado, $idmedidor);

    if ($stmt->execute()) {
        echo json_encode(["estado" => true, "mensaje" => "Datos Modificados Correctamente"]);
    } else {
        echo json_encode(["estado" => false, "mensaje" => "Error al modificar los datos"]);
    }
    $stmt->close();
    $conexion->close();
}

if ($opcion == "consultar") {
    require_once "../Config/Conexion.php";

    try {
        $sql = "SELECT m.idmedidor, m.idcontrato, s.dui, s.nombre, s.apellido,
                       c.ncontrato, m.nmedidor, r.nombre AS ruta, m.estado
                FROM medidor AS m
                INNER JOIN contrato AS c ON c.idcontrato = m.idcontrato
                INNER JOIN socio AS s ON s.idsocio = c.idsocio
                LEFT JOIN ruta AS r ON r.idRuta = m.idruta
                ORDER BY c.idcontrato";

        $resultado = $conexion->query($sql);
        $medidores = [];

        while ($fila = $resultado->fetch_assoc()) {
            $medidores[] = $fila;
        }

        echo json_encode($medidores, JSON_UNESCAPED_UNICODE);
        $resultado->free();
        $conexion->close();
    } catch (Throwable $e) {
        echo json_encode([
            "mensaje" => "No se pudieron obtener los medidores"
        ], JSON_UNESCAPED_UNICODE);
    }
}