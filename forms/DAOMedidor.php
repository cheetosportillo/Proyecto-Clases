<?php
header("Content-Type: application/json; charset=utf-8");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$opcion = $_POST["opcion"] ?? "";

if ($opcion === "guardar" || $opcion === "modificar") {
    require_once "../Config/Conexion.php";

    $idcontrato = $_POST["idcontrato"] ?? "";
    $idmedidor = $_POST["idmedidor"] ?? "";
    $nmedidor = trim($_POST["nmedidor"] ?? "");
    $inicio = trim($_POST["inicio"] ?? "");
    $canton = $_POST["canton"] ?? "";
    $ruta = $_POST["ruta"] ?? "";
    $direccion = trim($_POST["direccion"] ?? "");
    $referencia = trim($_POST["referencia"] ?? "");
    $estado = $_POST["estado"] ?? "";

    $idValido = static function ($valor): bool {
        return is_scalar($valor) && ctype_digit((string)$valor) && (int)$valor > 0;
    };

    if (
        ($opcion === "guardar" && !$idValido($idcontrato)) ||
        ($opcion === "modificar" && !$idValido($idmedidor)) ||
        $nmedidor === "" ||
        strlen($nmedidor) > 50 ||
        !preg_match('/^\d+(?:\.\d{1,2})?$/', $inicio) ||
        !$idValido($canton) ||
        !$idValido($ruta) ||
        $direccion === "" ||
        $referencia === "" ||
        !in_array($estado, ["Activo", "Suspendido", "Inactivo"], true)
    ) {
        http_response_code(400);
        echo json_encode([
            "estado" => false,
            "mensaje" => "Revisa los datos obligatorios del medidor"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $lecturaInicial = (float)$inicio;

    try {
        if ($opcion === "guardar") {
            $stmt = $conexion->prepare("SELECT idmedidor FROM medidor WHERE idcontrato = ? LIMIT 1");
            $stmt->bind_param("i", $idcontrato);
            $stmt->execute();
            $existente = $stmt->get_result();
            $yaTieneMedidor = $existente->num_rows > 0;
            $stmt->close();

            if ($yaTieneMedidor) {
                http_response_code(409);
                echo json_encode([
                    "estado" => false,
                    "mensaje" => "Este contrato ya tiene un medidor; usa la opción Modificar"
                ], JSON_UNESCAPED_UNICODE);
                $conexion->close();
                exit;
            }

            $stmt = $conexion->prepare(
                "INSERT INTO medidor
                    (idcontrato, nmedidor, lectura_inicial, idcanton, idruta, direccion, referencia, estado)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "isdiisss",
                $idcontrato,
                $nmedidor,
                $lecturaInicial,
                $canton,
                $ruta,
                $direccion,
                $referencia,
                $estado
            );
            $stmt->execute();
            $stmt->close();
            echo json_encode([
                "estado" => true,
                "mensaje" => "Datos guardados correctamente"
            ], JSON_UNESCAPED_UNICODE);
        } else {
            $stmt = $conexion->prepare(
                "UPDATE medidor
                 SET nmedidor = ?, lectura_inicial = ?, idcanton = ?, idruta = ?,
                     direccion = ?, referencia = ?, estado = ?
                 WHERE idmedidor = ?"
            );
            $stmt->bind_param(
                "sdiisssi",
                $nmedidor,
                $lecturaInicial,
                $canton,
                $ruta,
                $direccion,
                $referencia,
                $estado,
                $idmedidor
            );
            $stmt->execute();
            $stmt->close();
            echo json_encode([
                "estado" => true,
                "mensaje" => "Datos modificados correctamente"
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch (Throwable $e) {
        error_log("Error al guardar el medidor: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            "estado" => false,
            "mensaje" => "No se pudieron guardar los datos del medidor"
        ], JSON_UNESCAPED_UNICODE);
    }

    $conexion->close();
    exit;
}

if ($opcion === "consultar") {
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

        $resultado->free();
        echo json_encode($medidores, JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        error_log("Error al consultar los medidores: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            "estado" => false,
            "mensaje" => "No se pudieron obtener los medidores"
        ], JSON_UNESCAPED_UNICODE);
    }

    $conexion->close();
    exit;
}

http_response_code(400);
echo json_encode([
    "estado" => false,
    "mensaje" => "La operación solicitada no es válida"
], JSON_UNESCAPED_UNICODE);
