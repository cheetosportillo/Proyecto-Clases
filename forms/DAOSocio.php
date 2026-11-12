<?php
$dui = $_POST['dui'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$profesion = $_POST['profesion'] ?? '';
$nit = $_POST['nit'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$idcanton = $_POST['idcanton'] ?? '';
$idsocio = $_POST['idsocio'] ?? '';

$opcion = $_POST['opcion'] ?? '';

function duiValido($dui)
{
    return preg_match('/^\d{8}-\d$/', $dui) === 1;
}

// Un DUI solo puede pertenecer a un socio (se ignora el socio que se esta modificando)
function duiDisponible($conexion, $dui, $idsocio = 0)
{
    $sql = "SELECT idsocio FROM socio WHERE dui=? AND idsocio<>?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $dui, $idsocio);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $disponible = $resultado->num_rows === 0;
    $stmt->close();

    return $disponible;
}

if ($opcion == "guardar") {
    require_once "../Config/Conexion.php";

    if (!duiValido($dui)) {
        echo json_encode(["estado" => false, "mensaje" => "El DUI no es válido"]);
        $conexion->close();
        exit;
    }
    if (!duiDisponible($conexion, $dui)) {
        echo json_encode(["estado" => false, "mensaje" => "Este DUI ya existe"]);
        $conexion->close();
        exit;
    }

    $sql = "INSERT INTO socio (dui, nombre, apellido, telefono, fecha_nacimiento, profesion, idcanton, nit, direccion) VALUES(?,?,?,?,?,?,?,?,?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssiss", $dui, $nombre, $apellido, $telefono, $fecha_nacimiento, $profesion, $idcanton, $nit, $direccion);
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
    require_once "../Config/Conexion.php";

    if (!duiValido($dui)) {
        echo json_encode(["estado" => false, "mensaje" => "El DUI no es válido"]);
        $conexion->close();
        exit;
    }
    if (!duiDisponible($conexion, $dui, (int)$idsocio)) {
        echo json_encode(["estado" => false, "mensaje" => "Este DUI ya pertenece a otro socio"]);
        $conexion->close();
        exit;
    }

    $sql = "UPDATE socio SET dui=?, nombre=?, apellido=?, telefono=?, fecha_nacimiento=?, profesion=?, idcanton=?, nit=?, direccion=? WHERE idsocio=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssissi", $dui, $nombre, $apellido, $telefono, $fecha_nacimiento, $profesion, $idcanton, $nit, $direccion, $idsocio);
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
    require_once "../Config/Conexion.php";

    try {
        $sql = "DELETE FROM socio WHERE idsocio=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $idsocio);
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
        // por ejemplo, si el socio tiene contratos asociados
        echo json_encode([
            "estado" => false,
            "mensaje" => "No se puede eliminar: el socio tiene datos relacionados"
        ]);
    }
    $conexion->close();
}

if ($opcion == "consultar") {
    require_once "../Config/Conexion.php";

    try {
        $sql = "SELECT s.idsocio, s.dui, s.nombre, s.apellido, s.telefono, s.fecha_nacimiento,
                       s.profesion, s.nit, s.direccion, c.nombre AS canton
                FROM socio AS s
                LEFT JOIN canton AS c ON c.id_canton = s.idcanton
                ORDER BY s.nombre, s.apellido";

        $resultado = $conexion->query($sql);

        if (!$resultado) {
            throw new Exception("Error al consultar los registros");
        }

        $registros = [];

        while ($fila = $resultado->fetch_assoc()) {
            $registros[] = [
                'idsocio'          => $fila['idsocio'],
                'dui'              => $fila['dui'],
                'nombre'           => $fila['nombre'],
                'apellido'         => $fila['apellido'],
                'telefono'         => $fila['telefono'],
                'fecha_nacimiento' => date('d-m-Y', strtotime($fila['fecha_nacimiento'])),
                'nit'              => $fila['nit'],
                'profesion'        => $fila['profesion'],
                'direccion'        => $fila['direccion'],
                'canton'           => $fila['canton']
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

/////////////// Consulta por id (cuando se viene desde la lista con ?id=)
if ($opcion == "consultarIndividual") {
    require_once "../Config/Conexion.php";

    $sql = "SELECT * FROM socio WHERE idsocio=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idsocio);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo json_encode([
            "estado" => true,
            "datos" => $resultado->fetch_assoc()
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

/////////////// Consulta por DUI (al terminar de escribir el DUI en el formulario)
if ($opcion == "consultarDui") {
    require_once "../Config/Conexion.php";

    $sql = "SELECT * FROM socio WHERE dui=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $dui);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo json_encode([
            "estado" => true,
            "datos" => $resultado->fetch_assoc()
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
