document.addEventListener("DOMContentLoaded", async () => {
    const contenedor = document.getElementById("users-table");
    const filtro = document.getElementById("table-filter");
    const paginacion = document.getElementById("table-pagination");
    const contador = document.getElementById("total-registros");
    const btnImprimir = document.getElementById("print-table");

    if (!contenedor) return;

    const tabla = new Tabulator(contenedor, {
        locale: "es-es",
        langs: {
            "es-es": {
                pagination: {
                    page_size: "Registros por página",
                    first: "Primera",
                    first_title: "Primera página",
                    last: "Última",
                    last_title: "Última página",
                    prev: "Anterior",
                    prev_title: "Página anterior",
                    next: "Siguiente",
                    next_title: "Página siguiente",
                    all: "Todos",
                    counter: {
                        showing: "Mostrando",
                        of: "de",
                        rows: "registros",
                        pages: "páginas"
                    }
                },
                data: {
                    loading: "Cargando...",
                    error: "Error al cargar los datos"
                }
            }
        },
        data: [],
        layout: "fitColumns",
        pagination: true,
        paginationElement: paginacion,
        paginationSize: 10,
        paginationSizeSelector: [10, 25, 50, 100],
        movableColumns: true,
        columns: [
            { title: "Corr.", formatter: "rownum", hozAlign: "center", headerSort: false, width: 70 },
            { title: "ID medidor", field: "idmedidor", visible: false },
            { title: "ID contrato", field: "idcontrato", visible: false },
            { title: "DUI", field: "dui" },
            { title: "Nombre", field: "nombre" },
            { title: "Apellido", field: "apellido" },
            { title: "N. Contrato", field: "ncontrato" },
            { title: "N° Medidor", field: "nmedidor" },
            { title: "Ruta", field: "ruta" },
            {
                title: "Estado",
                field: "estado",
                hozAlign: "center",
                formatter: (cell) => {
                    const estado = String(cell.getValue() || "").toLocaleLowerCase();
                    if (estado === "activo") {
                        return '<i class="bi bi-check-circle-fill text-success" aria-label="Activo"></i>';
                    }
                    if (estado === "suspendido") {
                        return '<i class="bi bi-exclamation-circle-fill text-warning" aria-label="Suspendido"></i>';
                    }
                    return '<i class="bi bi-x-circle-fill text-danger" aria-label="Inactivo"></i>';
                }
            },
            {
                title: "Acciones",
                hozAlign: "center",
                headerSort: false,
                formatter: () => `
                    <select class="form-select form-select-sm accion-medidor" aria-label="Acciones del medidor">
                        <option value="">Seleccionar...</option>
                        <option value="modificar">Modificar</option>
                    </select>
                `,
                cellClick: (event, cell) => {
                    const select = event.target.closest(".accion-medidor");
                    if (!select) return;

                    select.onchange = () => {
                        if (select.value === "modificar") {
                            const medidor = cell.getRow().getData();
                            window.location.href = `medidor.php?id=${encodeURIComponent(medidor.idcontrato)}`;
                        }
                    };
                }
            }
        ]
    });

    const actualizarContador = () => {
        contador.textContent = `Total de Registros: ${tabla.getDataCount("active")}`;
    };

    tabla.on("dataLoaded", actualizarContador);
    tabla.on("dataFiltered", actualizarContador);
    tabla.on("dataChanged", actualizarContador);

    try {
        Swal.fire({
            title: "Cargando...",
            text: "Espere",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        const datos = new FormData();
        datos.append("opcion", "consultar");
        const respuesta = await fetch("DAOMedidor.php", { method: "POST", body: datos });
        if (!respuesta.ok) {
            throw new Error(`Error HTTP ${respuesta.status} al cargar los medidores`);
        }

        const medidores = await respuesta.json();
        if (!Array.isArray(medidores)) {
            throw new Error(medidores.mensaje || "Respuesta no válida al cargar los medidores");
        }

        await tabla.replaceData(medidores);
        actualizarContador();
    } catch (error) {
        console.error("Error al cargar medidores:", error);
        contenedor.textContent = "No se pudieron cargar los medidores.";
        await Swal.fire("No se pudieron cargar los medidores", error.message, "error");
    } finally {
        Swal.close();
    }

    filtro?.addEventListener("input", () => {
        const valor = filtro.value.trim();
        if (!valor) {
            tabla.clearFilter();
            return;
        }

        tabla.setFilter([
            [
                { field: "dui", type: "like", value: valor },
                { field: "nombre", type: "like", value: valor },
                { field: "apellido", type: "like", value: valor },
                { field: "ncontrato", type: "like", value: valor },
                { field: "nmedidor", type: "like", value: valor },
                { field: "ruta", type: "like", value: valor },
                { field: "estado", type: "like", value: valor }
            ]
        ]);
    });

    btnImprimir?.addEventListener("click", () => window.print());
});
