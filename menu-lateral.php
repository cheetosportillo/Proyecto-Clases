<?php
$paginaActual = basename($_SERVER['PHP_SELF']);

// Páginas del menú Configuración > Ruta
$paginasRuta = [
    'ruta.php',
    'lista-ruta.php'
];
$menuRutaActivo = in_array($paginaActual, $paginasRuta);

// Páginas del menú Configuración > Canton
$paginasCanton = [
    'canton.php',
    'lista-canton.php'
];
$menuCantonActivo = in_array($paginaActual, $paginasCanton);

// Páginas del menú Configuración > Cargo
$paginasCargo = [
    'cargo.php',
    'lista-cargo.php'
];
$menuCargoActivo = in_array($paginaActual, $paginasCargo);

// Páginas del menú Configuración, merge agrega mas opciones al vector
$paginasConfiguracion = array_merge(
    $paginasRuta,
    $paginasCanton,
    $paginasCargo,
    ['empresa.php', 'editors.php']
);

$menuConfiguracionActivo = in_array(
    $paginaActual,
    $paginasConfiguracion
);

// Páginas del menú Empleado
$paginasEmpleado = [
    'empleado.php',
    'lista-empleados.php',
    'asignarRuta.php'
];

$menuEmpleadoActivo = in_array($paginaActual, $paginasEmpleado);

// Páginas del menú Socio
$paginasSocio = [
    'socio.php',
    'lista-socio.php'
];

$menuSocioActivo = in_array($paginaActual, $paginasSocio);

// Páginas del menú Contrato
$paginasContrato = [
    'contrato.php',
    'lista-contrato.php'
];

$menuContratoActivo = in_array($paginaActual, $paginasContrato);

// Páginas del menú Medidor
$paginasMedidor = [
    'medidor.php',
    'lista-medidores.php'
];

$menuMedidorActivo = in_array($paginaActual, $paginasMedidor);

?>

<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="../index.html" class="brand-link">
            <!--begin::Brand Image-->
            <img src="../assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">Poseidón</span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Search-->
    <div class="sidebar-search" role="search">
        <label for="sidebar-search-input" class="visually-hidden">Filter menu</label>
        <input type="search" id="sidebar-search-input" class="form-control form-control-sm" placeholder="Filter menu…" autocomplete="off" data-lte-toggle="sidebar-search" data-lte-target="#navigation" />
        <p class="fs-7 text-secondary mt-2 mb-0" data-lte-search-empty role="status" hidden>
            No matching pages.
        </p>
    </div>
    <!--end::Sidebar Search-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="Main navigation">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" data-accordion="false" id="navigation">

                <!-- CONFIGURACIÓN -->
                <li class="nav-item <?= $menuConfiguracionActivo ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $menuConfiguracionActivo ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-circle-fill"></i>
                        <p>
                            Configuración
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                        <!-- RUTA -->
                        <li class="nav-item <?= $menuRutaActivo ? 'menu-open' : '' ?>">
                            <a href="#" class="nav-link <?= $menuRutaActivo ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>
                                    Ruta
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="ruta.php" class="nav-link <?= $paginaActual == 'ruta.php' ? 'active' : '' ?>">
                                        <i class="nav-icon bi bi-record-circle-fill"></i>
                                        <p>Nueva</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="lista-ruta.php" class="nav-link <?= $paginaActual == 'lista-ruta.php' ? 'active' : '' ?>">
                                        <i class="nav-icon bi bi-record-circle-fill"></i>
                                        <p>Consultar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- CANTON -->
                        <li class="nav-item <?= $menuCantonActivo ? 'menu-open' : '' ?>">
                            <a href="#" class="nav-link <?= $menuCantonActivo ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-crosshair"></i>
                                <p>
                                    Canton
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="canton.php" class="nav-link <?= $paginaActual == 'canton.php' ? 'active' : '' ?>">
                                        <i class="nav-icon bi bi-file-earmark-plus"></i>
                                        <p>Nuevo Canton</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="lista-canton.php" class="nav-link <?= $paginaActual == 'lista-canton.php' ? 'active' : '' ?>">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Consultar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- CARGO -->
                        <li class="nav-item <?= $menuCargoActivo ? 'menu-open' : '' ?>">
                            <a href="#" class="nav-link <?= $menuCargoActivo ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-briefcase"></i>
                                <p>
                                    Cargo
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="cargo.php" class="nav-link <?= $paginaActual == 'cargo.php' ? 'active' : '' ?>">
                                        <i class="nav-icon bi bi-file-earmark-plus"></i>
                                        <p>Nuevo Cargo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="lista-cargo.php" class="nav-link <?= $paginaActual == 'lista-cargo.php' ? 'active' : '' ?>">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Consultar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </li>

                <!-- EMPLEADO -->
                <li class="nav-item <?= $menuEmpleadoActivo ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $menuEmpleadoActivo ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-box-arrow-in-right"></i>
                        <p>
                            Empleado
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="empleado.php" class="nav-link <?= $paginaActual == 'empleado.php' ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Nuevo</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="lista-empleados.php" class="nav-link <?= $paginaActual == 'lista-empleados.php' ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Consulta</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="asignarRuta.php" class="nav-link <?= $paginaActual == 'asignarRuta.php' ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-signpost-split"></i>
                                <p>Asignar Ruta</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- SOCIO -->
                <li class="nav-item <?= $menuSocioActivo ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $menuSocioActivo ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-person"></i>
                        <p>
                            Socio
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="socio.php" class="nav-link <?= $paginaActual == 'socio.php' ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-person-plus"></i>
                                <p>Nuevo</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="lista-socio.php" class="nav-link <?= $paginaActual == 'lista-socio.php' ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-list"></i>
                                <p>Consulta</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- CONTRATO -->
                <li class="nav-item <?= $menuContratoActivo ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $menuContratoActivo ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-file-earmark-text"></i>
                        <p>
                            Contrato
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="contrato.php" class="nav-link <?= $paginaActual == 'contrato.php' ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>Nuevo</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="lista-contrato.php" class="nav-link <?= $paginaActual == 'lista-contrato.php' ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-list"></i>
                                <p>Consulta</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- MEDIDOR -->
                <li class="nav-item <?= $menuMedidorActivo ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $menuMedidorActivo ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>
                            Medidor
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="lista-medidores.php" class="nav-link <?= $paginaActual == 'lista-medidores.php' ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-list"></i>
                                <p>Consulta</p>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
