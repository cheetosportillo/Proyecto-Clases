document.addEventListener("DOMContentLoaded", function () {
    const contenedor = document.getElementById("users-table");
    const filtro = document.getElementById("table-filter");
    const paginacion = document.getElementById("table-pagination");
    const contador = document.getElementById("total-registros");

    if (!contenedor) return;

    const tabla = new Tabulator(contenedor, {
        ajaxURL: "DAOTarifa.php",
        ajaxParams: { opcion: "consultar" },
        ajaxConfig: "POST",
        layout: "fitColumns",
        pagination: true,
        paginationElement: paginacion,
        paginationSize: 10,
        columns: [
            { title: "Corr.", formatter: "rownum", width: 70, hozAlign: "center" },
            { title: "ID", field: "id_tarifa", visible: false },
            { title: "Desde (m³)", field: "desde", hozAlign: "center", formatter: cell => cell.getValue() + " m³" },
            { title: "Hasta (m³)", field: "hasta", hozAlign: "center", formatter: cell => cell.getValue() + " m³" },
            {
                title: "Tipo", field: "tipo", hozAlign: "center",
                formatter: cell => cell.getValue() === "FIJO"
                    ? '<span class="badge text-bg-primary">Fijo en rango</span>'
                    : '<span class="badge text-bg-info text-white">Por metro cúbico</span>'
            },
            {
                title: "Precio ($)", field: "precio", hozAlign: "right",
                formatter: cell => '<span class="text-success fw-bold">$' + parseFloat(cell.getValue()).toFixed(2) + '</span>'
            },
            {
                title: "Acciones", hozAlign: "center", width: 140,
                formatter: cell => {
                    const id = cell.getRow().getData().id_tarifa;
                    return `
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editarTarifa('${id}')"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarTarifa('${id}')"><i class="bi bi-trash"></i></button>
                    `;
                }
            }
        ]
    });

    filtro?.addEventListener("input", function () {
        const val = filtro.value.trim();
        tabla.setFilter("tipo", "like", val);
    });

    tabla.on("dataLoaded", data => { if (contador) contador.textContent = "Total de registros: " + data.length; });
});

window.editarTarifa = id => { window.location.href = "tarifa.php?id=" + id; };
window.eliminarTarifa = id => {
    Swal.fire({
        title: "¿Confirmar eliminación?", icon: "warning", showCancelButton: true, confirmButtonText: "Sí, eliminar"
    }).then(async r => {
        if (!r.isConfirmed) return;
        const d = new FormData();
        d.append("opcion", "eliminar");
        d.append("id_tarifa", id);
        const res = await fetch("DAOTarifa.php", { method: "POST", body: d });
        const j = await res.json();
        if (j.estado) location.reload();
    });
};