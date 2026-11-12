<?php
$dui = $_POST['dui'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$sueldo = $_POST['sueldo'] ?? '';
$afp = $_POST['id_afp'] ?? null;
if ($afp === '') $afp = null;
$nup = $_POST['nup'] ?? '';
$cargo = $_POST['id_cargo'] ?? '';
$fecha = $_POST['fecha_ingreso'] ?? '';
$renta = $_POST['id_renta'] ?? null;
if ($renta === '') $renta = null;
$canton = $_POST['id_canton'] ?? '';
$isss = $_POST['num_isss'] ?? '';
$estado = $_POST['id_estado'] ?? '';
$idempleado = $_POST['idempleado'] ?? '';

$opcion = isset($_REQUEST["opcion"]) ? $_REQUEST["opcion"] : "";

function duiValido($dui)
{
    return preg_match('/^\d{8}-\d$/', $dui) === 1;
}

function duiDisponible($conexion, $dui, $idempleado = 0)
{
    $sql = "SELECT idempleado FROM empleado WHERE dui=? AND idempleado<>?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $dui, $idempleado);
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

    $sql = "INSERT INTO empleado (dui, nombre, apellido, telefono, sueldo, id_afp, nup, id_cargo, fecha_ingreso, id_renta, id_canton, num_isss, id_estado) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssdisisiisi", $dui, $nombre, $apellido, $telefono, $sueldo, $afp, $nup, $cargo, $fecha, $renta, $canton, $isss, $estado);
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
    if (!duiDisponible($conexion, $dui, (int) $idempleado)) {
        echo json_encode(["estado" => false, "mensaje" => "Este DUI ya existe"]);
        $conexion->close();
        exit;
    }

    $sql = "UPDATE empleado SET dui=?, nombre=?, apellido=?, telefono=?, sueldo=?, id_afp=?, nup=?, id_cargo=?, fecha_ingreso=?, id_renta=?, id_canton=?, num_isss=?, id_estado=? WHERE idempleado=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssdisisiisii", $dui, $nombre, $apellido, $telefono, $sueldo, $afp, $nup, $cargo, $fecha, $renta, $canton, $isss, $estado, $idempleado);
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

    $sql = "DELETE FROM empleado WHERE idempleado=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idempleado);
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
    $conexion->close();
}

if ($opcion == "consultarDUI") {
    require_once "../Config/Conexion.php";

    $existe = !duiDisponible($conexion, $dui, (int) $idempleado);
    echo json_encode([
        "estado" => $existe,
        "mensaje" => $existe ? "Este DUI ya existe" : "DUI disponible"
    ]);
    $conexion->close();
}

if ($opcion == "consultar") {
    require_once "../Config/Conexion.php";

    try {
        $sql = "SELECT e.idempleado, e.dui, e.nombre, e.apellido, e.telefono, e.sueldo,
                       e.id_afp, afp.nombre AS afp, e.nup, e.id_cargo, cargo.nombre AS cargo,
                       e.fecha_ingreso, e.id_renta, renta.nombre AS renta, e.id_canton,
                       canton.nombre AS canton, e.num_isss, e.id_estado,
                       estado_empleado.nombre AS estado
                FROM empleado e
                LEFT JOIN afp ON afp.id_afp = e.id_afp
                LEFT JOIN cargo ON cargo.id_cargo = e.id_cargo
                LEFT JOIN renta ON renta.id_renta = e.id_renta
                LEFT JOIN canton ON canton.id_canton = e.id_canton
                LEFT JOIN estado_empleado ON estado_empleado.id_estado = e.id_estado
                ORDER BY e.apellido, e.nombre";

        $resultado = $conexion->query($sql);

        if (!$resultado) {
            throw new Exception("Error al consultar empleados");
        }

        $empleados = [];

        while ($fila = $resultado->fetch_assoc()) {
            $empleados[] = $fila;
        }

        echo json_encode(
            $empleados,
            JSON_UNESCAPED_UNICODE
        );

        $resultado->free();
        $conexion->close();
    } catch (Throwable $e) {

        echo json_encode([
            "mensaje" => "No se pudieron obtener los registros"
        ], JSON_UNESCAPED_UNICODE);
    }
}
if ($opcion == "consultarIndividual") {
    require_once "../Config/Conexion.php";

    $sql = "SELECT idempleado, dui, nombre, apellido, telefono, sueldo, id_afp, nup,
                   id_cargo, fecha_ingreso, id_renta, id_canton, num_isss, id_estado
            FROM empleado WHERE idempleado=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idempleado);

    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $datos = $resultado->fetch_assoc();
        echo json_encode([
            "estado" => true,
            "datos" => $datos
        ]);
    } else {
        echo json_encode([
            "estado" => false,
            "datos" => "No Existe"
        ]);
    }

    $stmt->close();
    $conexion->close();
}

if ($opcion == "consultarCatalogos") {
    require_once "../Config/Conexion.php";

    $catalogos = [
        "afp" => ["tabla" => "afp", "id" => "id_afp"],
        "renta" => ["tabla" => "renta", "id" => "id_renta"],
        "estado" => ["tabla" => "estado_empleado", "id" => "id_estado"]
    ];
    $resultado = [];

    foreach ($catalogos as $nombre => $catalogo) {
        $sql = "SELECT {$catalogo['id']} AS id, nombre FROM {$catalogo['tabla']} WHERE estado=1 ORDER BY nombre";
        $consulta = $conexion->query($sql);
        if (!$consulta) {
            echo json_encode(["estado" => false, "mensaje" => "No se pudo cargar el catálogo de {$nombre}"]);
            $conexion->close();
            exit;
        }

        $resultado[$nombre] = $consulta->fetch_all(MYSQLI_ASSOC);
        $consulta->free();
    }

    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    $conexion->close();
}
