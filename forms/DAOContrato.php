<?php
$idsocio = $_POST['idsocio'] ?? '';
$dui = $_POST['dui'] ?? '';
$empleado1 = $_POST['empleado1'] ?? 0;
$empleado2 = $_POST['empleado2'] ?? 0;
$cargo1 = $_POST['cargo1'] ?? '';
$cargo2 = $_POST['cargo2'] ?? '';
$numero = $_POST['numero'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$monto = $_POST['monto'] ?? 0;
$prima = $_POST['prima'] ?? 0;   // la prima no se captura en este formulario, se guarda en 0

$idcontrato = $_POST['idcontrato'] ?? '';

$opcion = $_POST['opcion'] ?? '';

// El segundo testigo es opcional: si no se elige, se guarda 0 y sin cargo
if ($empleado2 === '' || $empleado2 === null) $empleado2 = 0;
if ($empleado2 == 0) $cargo2 = '';

/////////////// GUARDAR
if ($opcion == "guardar") {
    require_once "../Config/Conexion.php";

    // se consulta el ultimo contrato y se le suma uno
    $ncontrato = getNumeroContrato($conexion);

    $sql = "INSERT INTO contrato (ncontrato, fecha, monto, prima, idsocio, idempleado1, cargo1, idempleado2, cargo2) VALUES (?,?,?,?,?,?,?,?,?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssddiisis", $ncontrato, $fecha, $monto, $prima, $idsocio, $empleado1, $cargo1, $empleado2, $cargo2);
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

/////////////// MODIFICAR (el socio y el numero de contrato no se cambian)
if ($opcion == "modificar") {
    require_once "../Config/Conexion.php";

    $sql = "UPDATE contrato SET fecha=?, monto=?, idempleado1=?, cargo1=?, idempleado2=?, cargo2=? WHERE idcontrato=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sdisisi", $fecha, $monto, $empleado1, $cargo1, $empleado2, $cargo2, $idcontrato);
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

/////////////// ELIMINAR
if ($opcion == "eliminar") {
    require_once "../Config/Conexion.php";

    try {
        $sql = "DELETE FROM contrato WHERE idcontrato=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $idcontrato);
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
    } catch (Throwable $e) {
        // por ejemplo, si el contrato ya tiene medidor o consumos asociados
        echo json_encode([
            "estado" => false,
            "mensaje" => "No se puede eliminar: el contrato tiene datos relacionados"
        ]);
    }
    $conexion->close();
}

/////////////// BUSCAR SOCIO POR DUI (para el formulario de contrato)
if ($opcion == "consultarDUI") {
    require_once "../Config/Conexion.php";

    $sql = "SELECT idsocio, dui, nombre, apellido FROM socio WHERE dui=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $dui);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        echo json_encode([
            "estado" => true,
            "datos" => [
                'idsocio'  => $fila['idsocio'],
                'dui'      => $fila['dui'],
                'nombre'   => $fila['nombre'],
                'apellido' => $fila['apellido']
            ]
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "estado" => false,
            "datos" => "No Existe"
        ]);
    }

    $stmt->close();
    $conexion->close();
}

/////////////// LISTA DE CONTRATOS
if ($opcion == "consultar") {
    require_once "../Config/Conexion.php";

    try {
        // "asignado" indica si el contrato ya tiene medidor (tabla medidor, columna idcontrato)
        $sql = "SELECT c.idcontrato, s.dui, s.nombre, s.apellido, c.ncontrato, c.fecha, c.monto,
                       IF(EXISTS(SELECT 1 FROM medidor AS m WHERE m.idcontrato = c.idcontrato),
                          'Asignado', 'Sin asignar') AS asignado
                FROM contrato AS c
                INNER JOIN socio AS s ON s.idsocio = c.idsocio
                ORDER BY c.idcontrato";

        try {
            $resultado = $conexion->query($sql);
        } catch (Throwable $e) {
            $resultado = false;
        }

        // Si la tabla medidor todavia no tiene esa columna, se lista sin la marca de medidor
        if (!$resultado) {
            $sql = "SELECT c.idcontrato, s.dui, s.nombre, s.apellido, c.ncontrato, c.fecha, c.monto,
                           'Sin asignar' AS asignado
                    FROM contrato AS c
                    INNER JOIN socio AS s ON s.idsocio = c.idsocio
                    ORDER BY c.idcontrato";
            $resultado = $conexion->query($sql);
        }

        if (!$resultado) {
            throw new Exception("Error al consultar los contratos");
        }

        $registros = [];

        while ($fila = $resultado->fetch_assoc()) {
            $registros[] = [
                'idcontrato' => $fila['idcontrato'],
                'dui'        => $fila['dui'],
                'nombre'     => $fila['nombre'],
                'apellido'   => $fila['apellido'],
                'ncontrato'  => $fila['ncontrato'],
                'fecha'      => date('d-m-Y', strtotime($fila['fecha'])),
                'monto'      => number_format((float)$fila['monto'], 2, '.', ','),
                'asignado'   => $fila['asignado']
            ];
        }

        echo json_encode($registros, JSON_UNESCAPED_UNICODE);

        $resultado->free();
        $conexion->close();
    } catch (Throwable $e) {
        echo json_encode([
            "mensaje" => "No se pudieron obtener los registros"
        ], JSON_UNESCAPED_UNICODE);
    }
}

/////////////// UN CONTRATO (cuando se viene desde la lista con ?id=)
if ($opcion == "consultarIndividual") {
    require_once "../Config/Conexion.php";

    $sql = "SELECT c.idcontrato, s.idsocio, s.dui, s.nombre, s.apellido, c.ncontrato, c.fecha, c.monto,
                   c.idempleado1, c.cargo1, c.idempleado2, c.cargo2
            FROM contrato AS c
            INNER JOIN socio AS s ON s.idsocio = c.idsocio
            WHERE c.idcontrato = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idcontrato);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        echo json_encode([
            "estado" => true,
            "datos" => [
                'idcontrato'  => $fila['idcontrato'],
                'idsocio'     => $fila['idsocio'],
                'dui'         => $fila['dui'],
                'nombre'      => $fila['nombre'],
                'apellido'    => $fila['apellido'],
                'ncontrato'   => $fila['ncontrato'],
                'fecha'       => $fila['fecha'],
                'monto'       => $fila['monto'],
                'idempleado1' => $fila['idempleado1'],
                'cargo1'      => $fila['cargo1'],
                'idempleado2' => $fila['idempleado2'],
                'cargo2'      => $fila['cargo2']
            ]
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "estado" => false,
            "datos" => "No Existe"
        ]);
    }

    $stmt->close();
    $conexion->close();
}

/////////////// Siguiente numero de contrato (el mayor + 1)
function getNumeroContrato($conexion)
{
    // ncontrato es texto, por eso se convierte a numero antes de buscar el maximo
    $sql = "SELECT MAX(CAST(ncontrato AS UNSIGNED)) AS ncontrato FROM contrato";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $numero = 0;

    if ($fila = $resultado->fetch_assoc()) {
        $numero = (int)$fila['ncontrato'];
    }

    $stmt->close();

    return $numero + 1;
}
