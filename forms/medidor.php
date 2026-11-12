<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="color-scheme" content="light dark" />
  <title>Sistema de Agua - POSEIDÓN</title>
  <script src="../js/color-thema.js"></script>
  <link rel="preload" href="../css/adminlte.css" as="style" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.3.0/index.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.16.0/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="../css/adminlte.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <div class="app-wrapper">
    <?php
    include "../menu-cabecera.php";
    include "../menu-lateral.php";
    ?>
    <main class="app-main">
      <div class="app-content-header">
        <div class="container-fluid">
          <div class="row"></div>
        </div>
      </div>
      <div class="app-content">
        <div class="container-fluid">
<form id="formulario" name="formulario" method="POST" action="#" autocomplete="off">
  <div class="row g-4">
    <div class="col-12">
      <div class="card card-secondary card-outline">
        <div class="card-header"><div class="card-title">Asignación de Medidor a Contrato</div></div>
      </div>
    </div>
    <!-- 1. Información del Socio -->
    <div class="col-12 col-md-6">
      <div class="card card-secondary card-outline mb-4 h-100">
        <div class="card-header"><div class="card-title">Información del Socio</div></div>
        <div class="card-body">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="dui" name="dui" placeholder="DUI (*)" readonly />
            <label for="dui">DUI (*)</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre (*)" readonly />
            <label for="nombre">Nombre del Socio (*)</label>
          </div>
        </div>
      </div>
    </div>
    <!-- 2. Información del Contrato -->
    <div class="col-12 col-md-6">
      <div class="card card-secondary card-outline mb-4 h-100">
        <div class="card-header"><div class="card-title">Información del Contrato</div></div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-5">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="numero" name="numero" placeholder="Número Contrato (*)" readonly />
                <label for="numero">Número Contrato (*)</label>
              </div>
            </div>
            <div class="col-12 col-md-7">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="fecha" name="fecha" placeholder="Fecha (*)" readonly />
                <label for="fecha">Fecha de Generación de Contrato (*)</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- 3. Ubicación Física del Medidor -->
    <div class="col-12 col-md-6">
      <div class="card card-secondary card-outline mb-4 h-100">
        <div class="card-header"><div class="card-title">Ubicación Física del Medidor</div></div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-6">
              <div class="form-floating mb-3">
                <select class="form-select" id="canton" name="canton" required><option value="0">[SELECCIONE]</option></select>
                <label for="canton">Seleccione Cantón (*)</label>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="form-floating mb-3">
                <select class="form-select" id="ruta" name="ruta" required><option value="0">[SELECCIONE]</option></select>
                <label for="ruta">Seleccione Ruta (*)</label>
              </div>
            </div>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección Exacta (*)" required />
            <label for="direccion">Dirección Exacta del Medidor (*)</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="referencia" name="referencia" placeholder="Punto de Referencia (*)" required />
            <label for="referencia">Punto de Referencia de la Ubicación (*)</label>
          </div>
        </div>
      </div>
    </div>
    <!-- 4. Datos del Medidor -->
    <div class="col-12 col-md-6">
      <div class="card card-secondary card-outline mb-4 h-100">
        <div class="card-header"><div class="card-title">Datos del Medidor</div></div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-6">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="nmedidor" name="nmedidor" placeholder="N° Medidor (*)" maxlength="50" required />
                <label for="nmedidor">N° Medidor (*)</label>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="form-floating mb-3">
                <input type="number" class="form-control" id="inicio" name="inicio" placeholder="Metros Iniciales (*)" min="0" step="0.01" required />
                <label for="inicio">Metros Iniciales (*)</label>
              </div>
            </div>
          </div>
          <div class="form-floating mb-3">
            <select class="form-select" id="estado" name="estado" required>
              <option value="0">[SELECCIONE]</option>
              <option value="Activo">Activo</option>
              <option value="Suspendido">Suspendido</option>
              <option value="Inactivo">Inactivo</option>
            </select>
            <label for="estado">Estado del Medidor (*)</label>
          </div>
        </div>
        <div class="card-footer d-flex w-100">
          <button type="button" class="btn btn-primary" id="btnGuardar" disabled>Guardar</button>
          <button type="button" class="btn btn-success ms-auto" id="btnCancelar">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
</form>
        </div>
      </div>
    </main>
    <?php include "../pie.php"; ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.16.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
  <script src="../js/adminlte.js"></script>
  <script src="../js/para-el-menu.js"></script>
  <script src="../js/JSMedidor.js?v=<?php echo filemtime('../js/JSMedidor.js'); ?>"></script>
</body>
</html>