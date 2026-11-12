<!DOCTYPE html>
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
  <!-- Skip links will be dynamically added by accessibility.js -->
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
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <div class="app-wrapper">
    <!--begin::Header-->
    <!--begin::Header-->
    <?php
    include "../menu-cabecera.php";
    include "../menu-lateral.php";
    ?>
    <!--end::Sidebar-->
    <!--end::Sidebar-->
    <main class="app-main">
      <div class="app-content-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6">
              <h1 class="mb-0 fs-3">Datos Empresa</h1>
            </div>
            <div class="col-sm-6">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item"><a href="#">Forms</a></li>
                  <li class="breadcrumb-item active" aria-current="page">
                    Elements
                  </li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </div>
      <div class="app-content">
        <div class="container-fluid">
          <div class="row g-4">
            <div class="col-12">
              <div class="callout callout-info">
                For detailed documentation visit
                <a href="https://getbootstrap.com/docs/5.3/forms/overview/" target="_blank" rel="noopener noreferrer" class="callout-link">Bootstrap Forms</a>.
              </div>
            </div>

            <!-- Quick Example -->
            <div class="col-md-6">
              <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                  <div class="card-title">Quick Example</div>
                </div>
                <form>
                  <div class="card-body">
                    <div class="mb-3">
                      <label for="exampleInputEmail1" class="form-label">Email address</label>
                      <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" />
                      <div id="emailHelp" class="form-text">
                        We'll never share your email with anyone else.
                      </div>
                    </div>
                    <div class="mb-3">
                      <label for="exampleInputPassword1" class="form-label">Password</label>
                      <input type="password" class="form-control" id="exampleInputPassword1" />
                    </div>
                    <div class="input-group mb-3">
                      <input type="file" class="form-control" id="inputGroupFile02" />
                      <label class="input-group-text" for="inputGroupFile02">Upload</label>
                    </div>
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="exampleCheck1" />
                      <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div>
                  </div>
                  <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                      Submit
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Input Group -->
            <div class="col-md-6">
              <div class="card card-success card-outline mb-4">
                <div class="card-header">
                  <div class="card-title">Input Group</div>
                </div>
                <div class="card-body">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">@</span>
                    <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1" />
                  </div>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2" />
                    <span class="input-group-text" id="basic-addon2">@example.com</span>
                  </div>
                  <div class="mb-3">
                    <label for="basic-url" class="form-label">Your vanity URL</label>
                    <div class="input-group">
                      <span class="input-group-text" id="basic-addon3">https://example.com/users/</span>
                      <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3 basic-addon4" />
                    </div>
                    <div class="form-text" id="basic-addon4">
                      Example help text goes outside the input group.
                    </div>
                  </div>
                  <div class="input-group mb-3">
                    <span class="input-group-text">$</span>
                    <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)" />
                    <span class="input-group-text">.00</span>
                  </div>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Username" aria-label="Username" />
                    <span class="input-group-text">@</span>
                    <input type="text" class="form-control" placeholder="Server" aria-label="Server" />
                  </div>
                  <div class="input-group">
                    <span class="input-group-text">With textarea</span>
                    <textarea class="form-control" aria-label="With textarea"></textarea>
                  </div>
                </div>
              </div>
            </div>

            <!-- Checks & Radios -->
            <div class="col-md-6">
              <div class="card card-info card-outline mb-4">
                <div class="card-header">
                  <div class="card-title">Checks &amp; Radios</div>
                </div>
                <div class="card-body">
                  <h6 class="text-secondary small text-uppercase mb-2">
                    Checkboxes
                  </h6>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="check-default" />
                    <label class="form-check-label" for="check-default">Default</label>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="check-checked" checked />
                    <label class="form-check-label" for="check-checked">Pre-checked</label>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="check-disabled" disabled />
                    <label class="form-check-label" for="check-disabled">Disabled</label>
                  </div>

                  <h6 class="text-secondary small text-uppercase mb-2 mt-3">
                    Radios
                  </h6>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="radioGroup" id="radio-1" checked />
                    <label class="form-check-label" for="radio-1">Option one</label>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="radioGroup" id="radio-2" />
                    <label class="form-check-label" for="radio-2">Option two</label>
                  </div>

                  <h6 class="text-secondary small text-uppercase mb-2 mt-3">
                    Switches
                  </h6>
                  <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="switch-1" />
                    <label class="form-check-label" for="switch-1">Notifications</label>
                  </div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="switch-2" checked />
                    <label class="form-check-label" for="switch-2">Auto-save</label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Select / Range / File -->
            <div class="col-md-6">
              <div class="card card-warning card-outline mb-4">
                <div class="card-header">
                  <div class="card-title">Selects, Ranges &amp; File</div>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label" for="select-default">Select</label>
                    <select class="form-select" id="select-default">
                      <option selected>Open this select menu</option>
                      <option>One</option>
                      <option>Two</option>
                      <option>Three</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="select-multiple">Multi-select</label>
                    <select class="form-select" id="select-multiple" multiple size="3">
                      <option>Apple</option>
                      <option selected>Orange</option>
                      <option>Pear</option>
                      <option>Mango</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="range-1">Range</label>
                    <input type="range" class="form-range" id="range-1" min="0" max="100" value="35" />
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="form-file-multi">Multiple files</label>
                    <input class="form-control" type="file" id="form-file-multi" multiple />
                  </div>
                  <div>
                    <label class="form-label" for="form-color">Color</label>
                    <input type="color" class="form-control form-control-color" id="form-color" value="#0d6efd" title="Choose your color" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Floating labels -->
            <div class="col-md-6">
              <div class="card card-secondary card-outline mb-4">
                <div class="card-header">
                  <div class="card-title">Floating Labels</div>
                </div>
                <div class="card-body">
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingEmail" placeholder="name@example.com" />
                    <label for="floatingEmail">Email address</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="floatingPwd" placeholder="Password" />
                    <label for="floatingPwd">Password</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="floatingTextarea" placeholder="Leave a comment here" style="height: 6rem"></textarea>
                    <label for="floatingTextarea">Comments</label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Disabled / Readonly -->
            <div class="col-md-6">
              <div class="card card-danger card-outline mb-4">
                <div class="card-header">
                  <div class="card-title">Disabled &amp; Readonly</div>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label" for="dis-text">Disabled text</label>
                    <input type="text" class="form-control" id="dis-text" value="Can't touch this" disabled />
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="ro-text">Readonly text</label>
                    <input type="text" class="form-control" id="ro-text" value="Read me, but don't write me" readonly />
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="dis-select">Disabled select</label>
                    <select id="dis-select" class="form-select" disabled>
                      <option>Locked option</option>
                    </select>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="dis-check" disabled />
                    <label class="form-check-label" for="dis-check">Can't check this</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    <!--begin::Footer-->
    <footer class="app-footer">
      <!--begin::To the end-->
      <div class="float-end d-none d-sm-inline">Anything you want</div>
      <!--end::To the end-->
      <!--begin::Copyright-->
      <strong>
        Copyright &copy; 2014-2026&nbsp;
        <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
      </strong>
      All rights reserved.
      <!--end::Copyright-->
    </footer>
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

</body>

</html>