<?php
header("Content-Type: application/json; charset=utf-8");

$opcion = $_POST["opcion"] ?? "";

function asegurarTirajeInicial(mysqli $conexion): void
{
    $conexion->begin_transaction();

    try {
        $resultado = $conexion->query("SELECT idtiraje FROM tiraje ORDER BY idtiraje LIMIT 1 FOR UPDATE");
        $hayRegistros = $resultado->num_rows > 0;
        $resultado->free();

        if (!$hayRegistros) {
            $prefijo = (int)date("Y");
            $stmt = $conexion->prepare(
                "INSERT INTO tiraje (prefijo, ultimo, estado)
                 VALUES (?, 0, 'Activo')
                 ON DUPLICATE KEY UPDATE idtiraje = LAST_INSERT_ID(idtiraje)"
            );
            $stmt->bind_param("i", $prefijo);
            $stmt->execute();
            $stmt->close();
        }

        $conexion->commit();
    } catch (Throwable $e) {
        $conexion->rollback();
        throw $e;
    }
}

if ($opcion === "consultar") {
    require_once "../Config/Conexion.php";

    try {
        asegurarTirajeInicial($conexion);

        $resultado = $conexion->query(
            "SELECT idtiraje, prefijo, ultimo, estado
             FROM tiraje
             ORDER BY prefijo DESC"
        );
        $tirajes = [];

        while ($fila = $resultado->fetch_assoc()) {
            $tirajes[] = $fila;
        }

        $resultado->free();
        echo json_encode($tirajes, JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        error_log("Error al consultar el tiraje: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            "estado" => false,
            "mensaje" => "No se pudo cargar la configuración de recibos"
        ], JSON_UNESCAPED_UNICODE);
    }

    $conexion->close();
    exit;
}

if ($opcion === "crearNuevo") {
    require_once "../Config/Conexion.php";
    $transaccionIniciada = false;

    try {
        $conexion->begin_transaction();
        $transaccionIniciada = true;

        $resultado = $conexion->query(
            "SELECT prefijo
             FROM tiraje
             ORDER BY prefijo DESC
             LIMIT 1
             FOR UPDATE"
        );
        $fila = $resultado->fetch_assoc();
        $resultado->free();

        $ultimoPrefijo = $fila ? (int)$fila["prefijo"] : 0;
        $prefijo = max((int)date("Y"), $ultimoPrefijo + 1);

        $conexion->query("UPDATE tiraje SET estado = 'Inactivo' WHERE estado = 'Activo'");

        $stmt = $conexion->prepare(
            "INSERT INTO tiraje (prefijo, ultimo, estado)
             VALUES (?, 0, 'Activo')"
        );
        $stmt->bind_param("i", $prefijo);
        $stmt->execute();
        $idtiraje = $conexion->insert_id;
        $stmt->close();

        $conexion->commit();
        $transaccionIniciada = false;

        echo json_encode([
            "estado" => true,
            "mensaje" => "Se creó el tiraje para el periodo " . $prefijo,
            "datos" => [
                "idtiraje" => $idtiraje,
                "prefijo" => $prefijo,
                "ultimo" => 0,
                "estado" => "Activo"
            ]
        ], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        if ($transaccionIniciada) {
            $conexion->rollback();
        }
        error_log("Error al crear el tiraje: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            "estado" => false,
            "mensaje" => "No se pudo crear el nuevo tiraje"
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
