document.addEventListener("DOMContentLoaded", async () => {
    const contenedor = document.getElementById("users-table");
    const filtro = document.getElementById("table-filter");
    const paginacion = document.getElementById("table-pagination");
    const contador = document.getElementById("total-registros");
    const btnImprimir = document.getElementById("print-table");

    if (!contenedor) return;

    const meses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];
    const formatearFecha = valor => {
        if (!valor) return "";
        const [anio, mes, dia] = String(valor).split("-");
        return dia ? `${dia}/${mes}/${anio}` : valor;
    };

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
                    error: "Error al cargar las lecturas"
                }
            }
        },
        data: [],
        layout: "fitColumns",
        pagination: true,
        paginationElement: paginacion,
        paginationSize: 10,
        paginationSizeSelector: [10, 25, 50, 100],
        placeholder: "No hay lecturas registradas",
        columns: [
            { title: "Corr.", formatter: "rownum", hozAlign: "center", headerSort: false, width: 70 },
            { title: "Medidor", field: "nmedidor", minWidth: 100 },
            { title: "Socio", field: "socio", minWidth: 150 },
            { title: "Período", field: "periodo", minWidth: 120 },
            { title: "Fecha lectura", field: "fecha_lectura", formatter: cell => formatearFecha(cell.getValue()), minWidth: 115 },
            { title: "Lectura anterior", field: "lectura_anterior", hozAlign: "right", formatter: cell => Number(cell.getValue()).toFixed(2), minWidth: 120 },
            { title: "Lectura actual", field: "lectura_actual", hozAlign: "right", formatter: cell => Number(cell.getValue()).toFixed(2), minWidth: 110 },
            { title: "Consumo (m³)", field: "consumo_m3", hozAlign: "right", formatter: cell => Number(cell.getValue()).toFixed(2), minWidth: 110 },
            { title: "Vence", field: "fecha_vencimiento", formatter: cell => formatearFecha(cell.getValue()), minWidth: 95 },
            { title: "Estado", field: "estado", hozAlign: "center", formatter: cell => {
                const estado = String(cell.getValue() || "");
                const badge = document.createElement("span");
                badge.className = `badge ${estado.toUpperCase() === "PENDIENTE" ? "text-bg-warning" : "text-bg-secondary"}`;
                badge.textContent = estado;
                return badge;
            }, minWidth: 100 },
            { title: "Elaboró", field: "empleado", minWidth: 130 },
            { title: "Aviso", field: "aviso", minWidth: 140 }
        ]
    });

    const actualizarContador = () => {
        contador.textContent = `Total de lecturas: ${tabla.getDataCount("active")}`;
    };

    tabla.on("dataLoaded", actualizarContador);
    tabla.on("dataFiltered", actualizarContador);

    try {
        Swal.fire({
            title: "Cargando lecturas...",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        const datos = new FormData();
        datos.append("opcion", "consultarLecturas");
        const respuesta = await fetch("DAOLectura.php", { method: "POST", body: datos });
        if (!respuesta.ok) {
            throw new Error(`Error HTTP ${respuesta.status} al cargar las lecturas`);
        }

        const lecturas = await respuesta.json();
        if (!Array.isArray(lecturas)) {
            throw new Error(lecturas.mensaje || "Respuesta no válida al cargar las lecturas");
        }

        await tabla.replaceData(lecturas.map(lectura => ({
            ...lectura,
            periodo: `${meses[Number(lectura.mes) - 1] || lectura.mes} ${lectura.anio}`
        })));
        actualizarContador();
    } catch (error) {
        console.error("Error al cargar las lecturas:", error);
        contenedor.textContent = "No se pudieron cargar las lecturas.";
        await Swal.fire("No se pudieron cargar las lecturas", error.message, "error");
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
                { field: "nmedidor", type: "like", value: valor },
                { field: "socio", type: "like", value: valor },
                { field: "periodo", type: "like", value: valor },
                { field: "fecha_lectura", type: "like", value: valor },
                { field: "estado", type: "like", value: valor },
                { field: "empleado", type: "like", value: valor },
                { field: "aviso", type: "like", value: valor }
            ]
        ]);
    });

    btnImprimir?.addEventListener("click", () => window.print());
});
