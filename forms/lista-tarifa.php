<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Sistema de Agua - POSEIDÓN | Tarifas Vigentes</title>
  <script src="../js/color-thema.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/adminlte.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tabulator-tables@6.6.1/dist/css/tabulator_bootstrap5.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <div class="app-wrapper">
    <?php include "../menu-cabecera.php"; include "../menu-lateral.php"; ?>
    <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">
          <div class="card card-secondary card-outline">
            <div class="card-header">
              <h3 class="card-title">Tarifas Vigentes</h3>
              <div class="card-tools">
                <input id="table-filter" type="search" class="form-control form-control-sm" placeholder="Filtrar por rango o tipo..." />
              </div>
            </div>
            <div class="card-body">
              <div class="d-flex gap-2 mb-3">
                <button id="print-table" type="button" class="btn btn-sm btn-outline-secondary">Imprimir</button>
              </div>
              <div id="users-table"></div>
              <div class="mt-2 text-end"><strong id="total-registros">Total de registros: 0</strong></div>
              <div id="table-pagination" class="d-flex justify-content-end mt-2"></div>
            </div>
          </div>
        </div>
      </div>
    </main>
    <?php include "../pie.php"; ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/tabulator-tables@6.6.1/dist/js/tabulator.min.js"></script>
  <script src="../js/JSLista-tarifa.js?v=<?php echo filemtime('../js/JSLista-tarifa.js'); ?>"></script>
</body>
</html>