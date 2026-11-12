<?php
$opcion = $_POST['opcion'] ?? '';

if ($opcion == "consultarConsumo") {
    require_once "../Config/Conexion.php";
    $nmedidor = $_POST['nmedidor'] ?? '';

    $sql = "SELECT m.idmedidor, concat(s.nombre, ' ', s.apellido) AS socio, m.nmedidor, m.estado, 
                   co.fecha_lectura AS fecha_anterior,
                   IF(co.lectura_actual IS NULL, m.lectura_inicial, co.lectura_actual) AS lectura_anterior,
                   ca.nombre AS canton, ru.nombre AS ruta, m.direccion
            FROM medidor AS m
            INNER JOIN contrato AS c ON c.idcontrato = m.idcontrato
            INNER JOIN socio AS s ON s.idsocio = c.idsocio
            INNER JOIN canton AS ca ON (ca.id_canton = m.idcanton OR ca.idcanton = m.idcanton)
            INNER JOIN ruta AS ru ON (ru.idRuta = m.idruta OR ru.idruta = m.idruta)
            LEFT JOIN consumo AS co ON co.idmedidor = m.idmedidor
            WHERE m.nmedidor = ?
            ORDER BY co.idconsumo DESC LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $nmedidor);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        echo json_encode(["estado" => true, "datos" => $row], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["estado" => false, "mensaje" => "Medidor no encontrado"]);
    }
    $stmt->close();
    $conexion->close();
}

if ($opcion == "guardar") {
    require_once "../Config/Conexion.php";
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $idmedidor         = $_POST['idmedidor'] ?? '';
        $idempleado        = $_POST['idempleado'] ?? '';
        $fecha_lectura     = $_POST['fecha_lectura'] ?? '';
        $fecha_anterior    = !empty($_POST['fecha_anterior']) ? $_POST['fecha_anterior'] : null;
        $lectura_anterior  = isset($_POST['lectura_anterior']) ? intval($_POST['lectura_anterior']) : 0;
        $lectura_actual    = isset($_POST['lectura_actual']) ? intval($_POST['lectura_actual']) : 0;
        $fecha_vencimiento = !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : null;
        $aviso             = $_POST['aviso'] ?? '';
        $mes               = isset($_POST['mes']) ? intval($_POST['mes']) : 0;
        $anio              = isset($_POST['anio']) ? intval($_POST['anio']) : 0;
        $estado            = 'PENDIENTE';

        $sql = "INSERT INTO consumo (idmedidor, idempleado, fecha_lectura, fecha_anterior, lectura_anterior, lectura_actual, fecha_vencimiento, aviso, mes, anio, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iissiisssis", $idmedidor, $idempleado, $fecha_lectura, $fecha_anterior, $lectura_anterior, $lectura_actual, $fecha_vencimiento, $aviso, $mes, $anio, $estado);

        if ($stmt->execute()) {
            echo json_encode(["estado" => true, "mensaje" => "Lectura Registrada Correctamente"]);
        } else {
            echo json_encode(["estado" => false, "mensaje" => "Error al registrar la lectura"]);
        }
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo json_encode(["estado" => false, "mensaje" => "Este medidor YA tiene registrado este PERIODO"]);
        } else {
            echo json_encode(["estado" => false, "mensaje" => "Error de base de datos: " . $e->getMessage()]);
        }
    }
    $conexion->close();
}