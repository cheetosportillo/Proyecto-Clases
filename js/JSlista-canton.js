document.addEventListener("DOMContentLoaded", function () {
  const tabla = new Tabulator("#users-table", {
    ajaxURL: "DAOCanton.php",
    ajaxParams: { opcion: "consultar" },
    ajaxConfig: "POST",
    ajaxResponse: function (url, params, response) {
      return response;
    },
    layout: "fitColumns",
    pagination: true,
    paginationSize: 10,
    paginationSizeSelector: [5, 10, 20, 50],
    paginationCounter: "rows",
    placeholder: "No hay cantones registrados",
    columns: [
     { title: "ID", field: "id_canton", width: 100, sorter: "number" },
      { title: "Nombre", field: "nombre", sorter: "string" },
      {
        title: "Acciones",
        hozAlign: "center",
        width: 140,
        headerSort: false,
        formatter: function (cell) {
          const id = cell.getRow().getData().id_canton;
          return `
            <button class="btn btn-sm btn-outline-primary me-1" onclick="editarCanton(${id})">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="eliminarCanton(${id})">
              <i class="bi bi-trash"></i>
            </button>`;
        },
      },
    ],
  });

  // Filtro del input de búsqueda
  const filtro = document.getElementById("table-filter");
  if (filtro) {
    filtro.addEventListener("input", function () {
      const valor = filtro.value.trim();
      tabla.setFilter("nombre", "like", valor);
    });
  }

  // Total de registros
  tabla.on("dataLoaded", function (data) {
    const total = document.getElementById("total-registros");
    if (total) total.textContent = "Total de Registros: " + data.length;
  });

  // Botón imprimir
  const btnImprimir = document.getElementById("print-table");
  if (btnImprimir) {
    btnImprimir.addEventListener("click", function () {
      tabla.print();
    });
  }
});

// --- Acciones ---

function editarCanton(id) {
  window.location.href = "canton.php?id=" + id;
}

function eliminarCanton(id) {
  Swal.fire({
    title: "¿Eliminar cantón?",
    text: "Esta acción no se puede deshacer",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then(async function (resultado) {
    if (!resultado.isConfirmed) return;

    try {
      const datos = new FormData();
      datos.append("opcion", "eliminar");
      datos.append("id_canton", id);

      const respuesta = await fetch("DAOCanton.php", { method: "POST", body: datos });
      if (!respuesta.ok) throw new Error("Error HTTP: " + respuesta.status);

      const json = await respuesta.json();
      if (json.estado) {
        Swal.fire({ position: "top-end", icon: "success", title: "Poseidón", text: json.mensaje, showConfirmButton: false, timer: 1500 });
        // Recargar la tabla
        location.reload();
      } else {
        Swal.fire({ position: "top-end", icon: "danger", title: "Poseidón", text: json.mensaje, showConfirmButton: false, timer: 1500 });
      }
    } catch (error) {
      Swal.fire({ position: "top-end", icon: "danger", title: "Poseidón", text: "No se pudo conectar con el servidor", showConfirmButton: false, timer: 1500 });
    }
  });
}
