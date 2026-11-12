<?php
$servidor = "localhost";
$usuario = "root";
$laclave = "";
$bbdd = "sys_agua";

$conexion = new mysqli("127.0.0.1:3307", "root", "", "sys_agua");


if ($conexion->connect_error) {
    die("Error de conexion:" . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");