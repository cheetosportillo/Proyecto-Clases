<?php
$empleado = $_POST['empleado'] ?? '';
$ruta = $_POST['ruta'] ?? '';
$idEmpleadoRuta = $_POST['idempleado_ruta'] ?? '';
$opcion = $_POST['opcion'] ?? '';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/////////////// TODOS LOS EMPLEADOS
if ($opcion == "consultarLectores") {
    require_once "../config/conexion.php";
    try {
        $sql = "SELECT e.idempleado, CONCAT(e.nombre,' ',e.apellido) AS nombre
                FROM empleado AS e
                ORDER BY e.nombre, e.apellido";
        $resultado = $conexion->query($sql);

        $empleados = [];
        while ($fila = $resultado->fetch_assoc()) {
            $empleados[] = [
                'idempleado' => $fila['idempleado'],
                'nombre'     => $fila['nombre']
            ];
        }
        echo json_encode($empleados, JSON_UNESCAPED_UNICODE);
        $resultado->free();
    } catch (Throwable $e) {
        echo json_encode([
            "estado"  => false,
            "mensaje" => "No se pudieron obtener los empleados"
        ], JSON_UNESCAPED_UNICODE);
    } finally {
        $conexion->close();
    }
}

/////////////// TODAS LAS RUTAS (para el select "Elija una Ruta")
if ($opcion == "consultarRutasDisponibles") {
    require_once "../config/conexion.php";
    try {
        $resultado = $conexion->query("SELECT idRuta, nombre FROM ruta ORDER BY nombre");
        $rutas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $rutas[] = [
                'idruta' => $fila['idRuta'],
                'nombre' => $fila['nombre']
            ];
        }
        echo json_encode($rutas, JSON_UNESCAPED_UNICODE);
        $resultado->free();
    } catch (Throwable $e) {
        echo json_encode([
            "estado"  => false,
            "mensaje" => "No se pudieron obtener las rutas"
        ], JSON_UNESCAPED_UNICODE);
    } finally {
        $conexion->close();
    }
}

/////////////// RUTAS QUE YA TIENE ASIGNADAS EL EMPLEADO
if ($opcion == "consultarRutas") {
    require_once "../config/conexion.php";
    try {
        $sql = "SELECT r.idRuta, r.nombre, er.idempleado_ruta
                FROM ruta AS r
                INNER JOIN empleado_ruta AS er ON r.idRuta = er.idruta
                WHERE er.idempleado = ?
                ORDER BY r.nombre";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $empleado);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $rutas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $rutas[] = [
                'idempleado_ruta' => $fila['idempleado_ruta'],
                'nombre'          => $fila['nombre']
            ];
        }
        echo json_encode([
            "estado" => true,
            "datos"  => $rutas
        ], JSON_UNESCAPED_UNICODE);
        $stmt->close();
    } catch (Throwable $e) {
        echo json_encode([
            "estado"  => false,
            "mensaje" => "No se pudieron obtener los registros"
        ], JSON_UNESCAPED_UNICODE);
    } finally {
        $conexion->close();
    }
}

/////////////// ASIGNAR RUTA
if ($opcion == "guardar") {
    require_once "../config/conexion.php";
    try {
        $sql = "INSERT INTO empleado_ruta (idempleado, idruta) VALUES (?,?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $empleado, $ruta);
        $stmt->execute();
        echo json_encode([
            "estado"  => true,
            "mensaje" => "Datos Guardados Correctamente"
        ]);
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        // 1062 = clave duplicada: el empleado ya tiene asignada esa ruta
        if ($e->getCode() == 1062) {
            echo json_encode([
                "estado"  => false,
                "mensaje" => "Este empleado ya tiene asignada esa ruta"
            ]);
        } else {
            echo json_encode([
                "estado"  => false,
                "mensaje" => "Ocurrió un error al guardar la ruta"
            ]);
        }
    } finally {
        $conexion->close();
    }
}

/////////////// QUITAR RUTA AL EMPLEADO
if ($opcion == "eliminar") {
    require_once "../config/conexion.php";
    try {
        $sql = "DELETE FROM empleado_ruta WHERE idempleado_ruta = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $idEmpleadoRuta);
        $stmt->execute();
        echo json_encode([
            "estado"  => true,
            "mensaje" => "Datos Eliminados Correctamente"
        ]);
        $stmt->close();
    } catch (Throwable $e) {
        echo json_encode([
            "estado"  => false,
            "mensaje" => "Error al eliminar los datos"
        ]);
    } finally {
        $conexion->close();
    }
}
