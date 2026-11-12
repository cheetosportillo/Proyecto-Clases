<?php
header("Content-Type: application/json; charset=utf-8");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$opcion = $_POST['opcion'] ?? '';

if ($opcion === "consultarLecturas") {
    require_once "../Config/Conexion.php";

    try {
        $sql = "SELECT co.idconsumo, m.nmedidor,
                       CONCAT_WS(' ', s.nombre, s.apellido) AS socio,
                       co.mes, co.anio, co.fecha_lectura, co.fecha_anterior,
                       co.lectura_anterior, co.lectura_actual,
                       (co.lectura_actual - co.lectura_anterior) AS consumo_m3,
                       co.fecha_vencimiento, co.aviso, co.estado,
                       CONCAT_WS(' ', e.nombre, e.apellido) AS empleado
                FROM consumo AS co
                LEFT JOIN medidor AS m ON m.idmedidor = co.idmedidor
                LEFT JOIN contrato AS c ON c.idcontrato = m.idcontrato
                LEFT JOIN socio AS s ON s.idsocio = c.idsocio
                LEFT JOIN empleado AS e ON e.idempleado = co.idempleado
                ORDER BY co.anio DESC, co.mes DESC, co.fecha_lectura DESC, co.idconsumo DESC";

        $resultado = $conexion->query($sql);
        $lecturas = $resultado->fetch_all(MYSQLI_ASSOC);
        $resultado->free();

        echo json_encode($lecturas, JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        error_log("Error al consultar las lecturas: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            "estado" => false,
            "mensaje" => "No se pudieron obtener las lecturas"
        ], JSON_UNESCAPED_UNICODE);
    } finally {
        $conexion->close();
    }
    exit;
}

if ($opcion === "consultarConsumo") {
    require_once "../Config/Conexion.php";
    $nmedidor = trim($_POST['nmedidor'] ?? '');
    $stmt = null;

    try {
        if ($nmedidor === '') {
            http_response_code(400);
            throw new InvalidArgumentException("Ingrese el número del medidor");
        }

        $sql = "SELECT m.idmedidor, CONCAT(s.nombre, ' ', s.apellido) AS socio,
                       m.nmedidor, m.estado, co.fecha_lectura AS fecha_anterior,
                       COALESCE(co.lectura_actual, m.lectura_inicial) AS lectura_anterior,
                       ca.nombre AS canton, ru.nombre AS ruta, m.direccion
                FROM medidor AS m
                INNER JOIN contrato AS c ON c.idcontrato = m.idcontrato
                INNER JOIN socio AS s ON s.idsocio = c.idsocio
                INNER JOIN canton AS ca ON ca.id_canton = m.idcanton
                INNER JOIN ruta AS ru ON ru.idRuta = m.idruta
                LEFT JOIN consumo AS co ON co.idmedidor = m.idmedidor
                WHERE m.nmedidor = ?
                ORDER BY co.idconsumo DESC
                LIMIT 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $nmedidor);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();

        if (!$fila) {
            echo json_encode([
                "estado" => false,
                "mensaje" => "No se encontró un medidor con ese número"
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "estado" => true,
                "datos" => $fila
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch (Throwable $e) {
        if (!($e instanceof InvalidArgumentException)) {
            error_log("Error al buscar medidor para lectura: " . $e->getMessage());
            http_response_code(500);
        }
        echo json_encode([
            "estado" => false,
            "mensaje" => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    } finally {
        if ($stmt instanceof mysqli_stmt) {
            $stmt->close();
        }
        $conexion->close();
    }
    exit;
}

if ($opcion == "guardar") {
    require_once "../Config/Conexion.php";
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $idmedidor         = $_POST['idmedidor'] ?? '';
        $idempleado        = $_POST['idempleado'] ?? '';
        $fecha_lectura     = $_POST['fecha_lectura'] ?? '';
        $fecha_anterior    = !empty($_POST['fecha_anterior']) ? $_POST['fecha_anterior'] : null;
        $lectura_anterior  = isset($_POST['lectura_anterior']) ? (float)$_POST['lectura_anterior'] : 0.0;
        $lectura_actual    = isset($_POST['lectura_actual']) ? (float)$_POST['lectura_actual'] : 0.0;
        $fecha_vencimiento = !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : null;
        $aviso             = $_POST['aviso'] ?? '';
        $mes               = isset($_POST['mes']) ? intval($_POST['mes']) : 0;
        $anio              = isset($_POST['anio']) ? intval($_POST['anio']) : 0;
        $estado            = 'PENDIENTE';

        $sql = "INSERT INTO consumo (idmedidor, idempleado, fecha_lectura, fecha_anterior, lectura_anterior, lectura_actual, fecha_vencimiento, aviso, mes, anio, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iissddssiis", $idmedidor, $idempleado, $fecha_lectura, $fecha_anterior, $lectura_anterior, $lectura_actual, $fecha_vencimiento, $aviso, $mes, $anio, $estado);

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