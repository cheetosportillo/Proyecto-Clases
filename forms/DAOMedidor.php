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