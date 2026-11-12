<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Sistema de Agua - POSEIDÓN | Configuración de Tarifas</title>
  <script src="../js/color-thema.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/adminlte.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <div class="app-wrapper">
    <?php include "../menu-cabecera.php"; include "../menu-lateral.php"; ?>
    <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">
          <form id="formulario" name="formulario" method="POST" action="#" autocomplete="off">
            <div class="row">
              <div class="col-12 col-md-3"></div>
              <div class="col-12 col-md-6">
                <div class="card card-secondary card-outline mb-4">
                  <div class="card-header"><div class="card-title">Rango de Precio de Tarifas por m³</div></div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-6 mb-3">
                        <label for="desde">Desde (*)</label>
                        <input type="number" step="0.01" class="form-control" id="desde" name="desde" required />
                      </div>
                      <div class="col-6 mb-3">
                        <label for="hasta">Hasta (*)</label>
                        <input type="number" step="0.01" class="form-control" id="hasta" name="hasta" required />
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-6 mb-3">
                        <label for="tipo">Tipo de Tarifa (*)</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                          <option value="">[Tipo Tarifa]</option>
                          <option value="FIJO">Tarifa Fija en este rango</option>
                          <option value="POR_METRO">Tarifa por metro</option>
                        </select>
                      </div>
                      <div class="col-6 mb-3">
                        <label for="precio">Precio ($) (*)</label>
                        <input type="number" step="0.01" class="form-control" id="precio" name="precio" required />
                      </div>
                    </div>
                  </div>
                  <div class="card-footer d-flex w-100">
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
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
  <script src="../js/adminlte.js"></script>
  <script src="../js/JSTarifa.js?v=<?php echo filemtime('../js/JSTarifa.js'); ?>"></script>
</body>
</html>