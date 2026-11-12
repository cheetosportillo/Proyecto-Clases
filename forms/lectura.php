<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Sistema de Agua - POSEIDÓN | Toma de Lectura</title>
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
            <input type="hidden" id="idmedidor" name="idmedidor" />
            <div class="row g-4">
              <!-- Información del Medidor -->
              <div class="col-12 col-lg-6">
                <div class="card card-secondary card-outline h-100">
                  <div class="card-header"><div class="card-title">Información del Medidor</div></div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label for="nmedidor" class="form-label fw-bold">Número de Medidor (*)</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                        <input type="text" class="form-control form-control-lg" id="nmedidor" name="nmedidor" placeholder="Escanear y presione Enter" autofocus required />
                        <button class="btn btn-outline-secondary" type="button" id="btnBuscarMedidor"><i class="bi bi-search"></i></button>
                      </div>
                    </div>
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" id="socio" name="socio" readonly />
                      <label for="socio">Propietario / Socio (*)</label>
                    </div>
                    <div class="row">
                      <div class="col-6"><input type="text" class="form-control" id="canton" readonly /></div>
                      <div class="col-6"><input type="text" class="form-control" id="ruta" readonly /></div>
                    </div>
                    <div class="mt-3"><input type="text" class="form-control" id="direccion" readonly /></div>
                  </div>
                </div>
              </div>

              <!-- Lectura Actual -->
              <div class="col-12 col-lg-6">
                <div class="card card-secondary card-outline h-100">
                  <div class="card-header"><div class="card-title">Lectura Actual</div></div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Lectura Actual (*)</label>
                        <input type="number" class="form-control form-control-lg" id="lectura_actual" required />
                      </div>
                      <div class="col-6 mb-3">
                        <label class="form-label">Lectura Anterior</label>
                        <input type="number" class="form-control" id="lectura_anterior" readonly />
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-6 mb-3">
                        <label class="form-label">Fecha Lectura (*)</label>
                        <input type="date" class="form-control" id="fecha_lectura" required />
                      </div>
                      <div class="col-6 mb-3">
                        <label class="form-label">Fecha Anterior</label>
                        <input type="date" class="form-control" id="fecha_anterior" readonly />
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Elaboró la Lectura (*)</label>
                      <select class="form-select" id="idempleado" required></select>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Facturación -->
              <div class="col-12">
                <div class="card card-secondary card-outline">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-3"><input type="number" class="form-control" id="anio" required /></div>
                      <div class="col-md-3">
                        <select class="form-select" id="mes" required>
                          <option value="1">1 - Enero</option><option value="2">2 - Febrero</option>
                          <option value="3">3 - Marzo</option><option value="4">4 - Abril</option>
                          <option value="5">5 - Mayo</option><option value="6">6 - Junio</option>
                          <option value="7">7 - Julio</option><option value="8">8 - Agosto</option>
                          <option value="9">9 - Septiembre</option><option value="10">10 - Octubre</option>
                          <option value="11">11 - Noviembre</option><option value="12">12 - Diciembre</option>
                        </select>
                      </div>
                      <div class="col-md-3"><input type="date" class="form-control" id="fecha_vencimiento" required /></div>
                      <div class="col-md-3"><input type="text" class="form-control" id="aviso" placeholder="Observaciones" /></div>
                    </div>
                  </div>
                  <div class="card-footer d-flex">
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar Lectura</button>
                    <button type="button" class="btn btn-secondary ms-auto" id="btnCancelar">Cancelar</button>
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
  <script src="../js/JSlectura.js?v=<?php echo filemtime('../js/JSlectura.js'); ?>"></script>
</body>
</html>