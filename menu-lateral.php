<?php
$paginaActual = basename($_SERVER['PHP_SELF']);

// Páginas del menú Configuración > Ruta
$paginasRuta = [
    'ruta.php',
    'lista-ruta.php'
];

$menuRutaActivo = in_array($paginaActual, $paginasRuta);

// Páginas del menú Configuración, merge agrega mas opciones al vector
$paginasConfiguracion = array_merge(
    $paginasRuta,
    ['empresa.php', 'editors.php']
);

$menuConfiguracionActivo = in_array(
    $paginaActual,
    $paginasConfiguracion
);

// Páginas del menú Empleado
$paginasEmpleado = [
    'empleado.php',
    'lista-empleados.php'
];

$menuEmpleadoActivo = in_array($paginaActual, $paginasEmpleado);

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

                <li class="nav-item <?= $menuConfiguracionActivo ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $menuConfiguracionActivo ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-circle-fill"></i>
                        <p>
                            Configuración
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">                        
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
                        
                    </ul>
                </li>


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
                    </ul>
                </li>

                <!--
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>
                            Dashboard
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="../index.html" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Dashboard v1</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../index2.html" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Dashboard v2</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../index3.html" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Dashboard v3</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../starter.html" class="nav-link">
                        <i class="nav-icon bi bi-file-earmark"></i>
                        <p>Starter Page</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../generate/theme.html" class="nav-link">
                        <i class="nav-icon bi bi-palette"></i>
                        <p>Theme Generate</p>
                    </a>
                </li>
                
                

                <li class="nav-header">MULTI LEVEL EXAMPLE</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-circle-fill"></i>
                        <p>Level 1</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-circle-fill"></i>
                        <p>
                            Level 1
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Level 2</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>
                                    Level 2
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon bi bi-record-circle-fill"></i>
                                        <p>Level 3</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon bi bi-record-circle-fill"></i>
                                        <p>Level 3</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon bi bi-record-circle-fill"></i>
                                        <p>Level 3</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Level 2</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-circle-fill"></i>
                        <p>Level 1</p>
                    </a>
                </li>

                <li class="nav-header">LABELS</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-circle text-danger"></i>
                        <p class="text">Important</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-circle text-warning"></i>
                        <p>Warning</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-circle text-info"></i>
                        <p>Informational</p>
                    </a>
                </li>
            </ul>-->

                <!--end::Sidebar Menu-->


        </nav>
    </div>
</aside>