<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="color-scheme" content="light dark" />
  <title>Sistema de Agua - POSEIDÓN | Números de Recibos</title>
  <script src="../js/color-thema.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.16.0/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="../css/adminlte.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <div class="app-wrapper">
    <?php include "../menu-cabecera.php"; include "../menu-lateral.php"; ?>
    <main class="app-main">
      <div class="app-content-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <h3 class="mb-0">Configuración de Números de Recibos</h3>
            </div>
          </div>
        </div>
      </div>
      <div class="app-content">
        <div class="container-fluid">
          <form id="formulario" autocomplete="off">
            <div class="row justify-content-center">
              <div class="col-12 col-md-6">
                <div class="card card-secondary card-outline">
                  <div class="card-header">
                    <div class="card-title">Configuración</div>
                  </div>
                  <div class="card-body">
                    <div class="row g-3">
                      <div class="col-12 col-md-6">
                        <label for="prefijo" class="form-label">Prefijo</label>
                        <select class="form-select" id="prefijo" required>
                          <option value="">[Seleccione]</option>
                        </select>
                      </div>
                      <div class="col-12 col-md-6">
                        <label for="ultimo" class="form-label">Último número generado (*)</label>
                        <input type="number" class="form-control" id="ultimo" min="0" readonly />
                      </div>
                      <div class="col-12 col-md-6">
                        <label for="estado" class="form-label">Estado</label>
                        <input type="text" class="form-control" id="estado" readonly />
                      </div>
                    </div>
                  </div>
                  <div class="card-footer d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="btnNuevo">
                      Nuevo Tiraje de números de factura
                    </button>
                    <button type="button" class="btn btn-success ms-auto" id="btnCancelar">
                      Cancelar
                    </button>
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
  <script src="../js/JSTiraje.js?v=<?php echo filemtime('../js/JSTiraje.js'); ?>"></script>
</body>
</html>
