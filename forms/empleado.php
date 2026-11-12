<<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Sistema de Agua - POSEIDÓN</title>

  <!--begin::Theme Init (prevents flash of incorrect theme on load, #6043)-->
  <script src="../js/color-thema.js"></script>
  <!--end::Theme Init-->

  <!--begin::Accessibility Meta Tags-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <meta name="color-scheme" content="light dark" />
  <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
  <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
  <!--end::Accessibility Meta Tags-->

  <!--begin::Primary Meta Tags-->
  <meta name="title" content="Sistema de Agua - POSEIDÓN" />  
  <meta name="description" content="Sistema Administrativo y Financiero de Agua" />
  <meta name="keywords" content="bootstrap 5, poseidon, agua potable, Sistema Administrativo y Financiero de Agua" />
  <!--end::Primary Meta Tags-->

  <!--begin::Accessibility Features-->
  <meta name="supported-color-schemes" content="light dark" />
  <link rel="preload" href="../css/adminlte.css" as="style" />
  <!--end::Accessibility Features-->

  <!--begin::Fonts-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.3.0/index.css" integrity="sha256-5UwXtC6TaPHCAbVQ59waEAqOssHyhn/twyuLRRWszzo=" crossorigin="anonymous" media="print" onload="this.media = 'all'" />
  <!--end::Fonts-->

  <!--begin::Third Party Plugin(OverlayScrollbars)-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.16.0/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
  <!--end::Third Party Plugin(OverlayScrollbars)-->

  <!--begin::Third Party Plugin(Bootstrap Icons)-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
  <!--end::Third Party Plugin(Bootstrap Icons)-->

  <!--begin::Required Plugin(AdminLTE)-->
  <link rel="stylesheet" href="../css/adminlte.css" />
  <!--end::Required Plugin(AdminLTE)-->
  
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <div class="app-wrapper">
    
    <!--begin::Header-->
    <?php
    include "../menu-cabecera.php";
    include "../menu-lateral.php";
    ?>
    <!--end::Sidebar-->

    <main class="app-main">
      <div class="app-content-header">
        <div class="container-fluid">
          <div class="row">
          </div>
        </div>
      </div>
      
      <div class="app-content">
        <div class="container-fluid">
          
          <form id="formulario" name="formulario" method="POST" action="#">
            <div class="row g-4">
              
              <!-- Columna Izquierda: Datos del Empleado -->
              <div class="col-12 col-md-6">
                <div class="card card-secondary card-outline mb-4 h-100">
                  <div class="card-header">
                    <div class="card-title">Datos del Empleado</div>
                  </div>
                  <div class="card-body">
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" id="dui" name="dui" placeholder="DUI(*)" maxlength="10" inputmode="numeric" autocomplete="off" required />
                      <label for="dui">DUI(*)</label>
                    </div>
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre(*)" required />
                      <label for="nombre">Nombre(*)</label>
                    </div>
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido(*)" required />
                      <label for="apellido">Apellido(*)</label>
                    </div>
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Telefono(*)" maxlength="9" inputmode="numeric" required />
                      <label for="telefono">Teléfono(*)</label>
                    </div>
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" id="num_isss" name="num_isss" placeholder="N° ISSS">
                      <label for="num_isss">N° ISSS</label>
                    </div>
                    <div class="form-floating mb-3">
                      <select class="form-select" id="id_afp" name="id_afp" aria-label="Seleccione la AFP">
                        <option value="">[SELECCIONE]</option>
                      </select>
                      <label for="id_afp">Seleccione la AFP</label>
                    </div>
                    <div class="form-floating mb-2">
                      <input type="text" class="form-control" id="nup" name="nup" placeholder="NUP">
                      <label for="nup">NUP</label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Columna Derecha: Otros Datos -->
              <div class="col-12 col-md-6">
                <div class="card card-secondary card-outline mb-4 h-100 d-flex flex-column">
                  <div class="card-header">
                    <div class="card-title">Otros Datos</div>
                  </div>
                  <div class="card-body flex-grow-1">
                    <div class="form-floating mb-3">
                      <select class="form-select" id="id_renta" name="id_renta" aria-label="Seleccione la RENTA">
                        <option value="">[SELECCIONE]</option>
                      </select>
                      <label for="id_renta">Seleccione la RENTA</label>
                    </div>
                    <div class="form-floating mb-3">
                      <input type="number" class="form-control" id="sueldo" name="sueldo" min="0" step="0.01" placeholder="Sueldo ($) (*)" required>
                      <label for="sueldo">Sueldo ($) (*)</label>
                    </div>
                    <div class="form-floating mb-3">
                      <select class="form-select" id="id_cargo" name="id_cargo" aria-label="Cargo del empleado (*)" required>
                        <option value="">[SELECCIONE UN CARGO]</option>
                      </select>
                      <label for="id_cargo">Cargo del empleado (*)</label>
                    </div>
                    <div class="form-floating mb-3">
                      <select class="form-select" id="id_canton" name="id_canton" aria-label="Donde vive el empleado (*)" required>
                        <option value="">[SELECCIONE UN CANTON]</option>
                      </select>
                      <label for="id_canton">Donde vive el empleado (*)</label>
                    </div>
                    <div class="form-floating mb-3">
                      <select class="form-select" id="id_estado" name="id_estado" aria-label="Seleccione Estado (*)" required>
                        <option value="">[SELECCIONE]</option>
                      </select>
                      <label for="id_estado">Seleccione Estado (*)</label>
                    </div>
                    <div class="form-floating mb-2">
                      <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" placeholder="Fecha de Ingreso(*)" required>
                      <label for="fecha_ingreso">Fecha de Ingreso(*)</label>
                    </div>
                  </div>
                  
                  <!-- Botones ajustados en el pie de la tarjeta derecha -->
                  <div class="card-footer text-end border-top">
                    <!-- Se conserva type="button" e id="btnGuardar" para tu script JS -->
                    <button type="button" class="btn btn-primary me-2" id="btnGuardar">Guardar</button>
                    <button type="button" class="btn btn-success" id="btnCancelar">Cancelar</button>
                  </div>
                </div>
              </div>

            </div>
          </form>

        </div>
      </div>
    </main>

    <!--begin::Footer-->
    <?php
    include "../pie.php";
    ?>
    <!--end::Footer-->
  </div>

  <!--begin::Third Party Plugin(OverlayScrollbars)-->
  <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.16.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
  <!--end::Third Party Plugin(OverlayScrollbars)-->
  <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
  <!--end::Required Plugin(popperjs for Bootstrap 5)-->
  <!--begin::Required Plugin(Bootstrap 5)-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
  <!--end::Required Plugin(Bootstrap 5)-->
  <!--begin::Required Plugin(AdminLTE)-->
  <script src="../js/adminlte.js"></script>
  <!--end::Required Plugin(AdminLTE)-->
  <!--begin::OverlayScrollbars Configure-->
  <script src="../js/para-el-menu.js"></script>
  <!--end::OverlayScrollbars Configure-->
  <script src="../js/JSempleado.js?v=<?php echo filemtime('../js/JSempleado.js'); ?>"></script>
</body>

</html>