
//////////PROCESA AL TERMINAR DE CARGAR LA PAGINA
document.addEventListener("DOMContentLoaded", async () => {
    const contenedor = document.getElementById("users-table");
    const filtro = document.getElementById("table-filter");
    const paginacion = document.getElementById("table-pagination");
    const btnImprimir = document.getElementById("print-table");

    const contador = document.getElementById("total-registros");

    if (!contenedor) return;
    const tabla = new Tabulator(contenedor, {
        locale: "es-es",
        langs: {
            "es-es": {
                "pagination": {
                    "page_size": "Registros por página",
                    "first": "Primera",
                    "first_title": "Primera página",
                    "last": "Última",
                    "last_title": "Última página",
                    "prev": "Anterior",
                    "prev_title": "Página anterior",
                    "next": "Siguiente",
                    "next_title": "Página siguiente",
                    "all": "Todos",
                    "counter": {
                        "showing": "Mostrando",
                        "of": "de",
                        "rows": "registros",
                        "pages": "páginas"
                    }
                },
                "data": {
                    "loading": "Cargando...",
                    "error": "Error al cargar los datos"
                },
                "headerFilters": {
                    "default": "Filtrar columna..."
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
            { title: "Corr.", field: "correlativo", formatter: "rownum", hozAlign: "center", headerSort: false, width: 70 },
            { title: "ID", field: "idcontrato", visible: false },
            { title: "DUI", field: "dui" },
            { title: "Nombre", field: "nombre" },
            { title: "Apellido", field: "apellido" },
            { title: "N. Contrato", field: "ncontrato" },
            { title: "Fecha", field: "fecha" },
            { title: "Monto ($)", field: "monto", hozAlign: "right" },
            {
                title: "Tiene Medidor?", field: "asignado", hozAlign: "center", headerSort: false,
                formatter: function (cell) {
                    const asignado = cell.getValue();

                    if (asignado === "Asignado") {
                        return `<span style="color: green; font-size: 20px;"
                                    title="Tiene medidor">
                                    <i class="bi bi-check-circle-fill"></i>
                                </span>`;
                    } else {
                        return `<span style="color: red; font-size: 20px;"
                                    title="No tiene medidor">
                                    <i class="bi bi-x-circle-fill"></i>
                                </span>`;
                    }
                }
            },
            {
                title: "Acciones", field: "acciones", hozAlign: "center", headerSort: false,
                formatter: function () {
                    return `
                        <select class="form-select form-select-sm accion-contrato">
                            <option value="">Seleccionar...</option>
                            <option value="modificar">Modificar</option>
                            <option value="eliminar">Eliminar</option>
                            <option value="medidor">Asignar Medidor</option>
                            <option value="imprimir">Imprimir</option>
                        </select>
                    `;
                },
                cellClick: function (e, cell) {
                    if (!e.target.matches(".accion-contrato")) return;

                    e.target.onchange = function () {
                        const accion = this.value;
                        const contrato = cell.getRow().getData();

                        if (accion === "modificar") {
                            location.href = "contrato.php?id=" + contrato.idcontrato;
                        }

                        if (accion === "medidor") {
                            location.href = "medidor.php?id=" + contrato.idcontrato;
                        }

                        if (accion === "imprimir") {
                            // la impresion del contrato se agrega mas adelante
                            this.value = "";
                        }

                        if (accion === "eliminar") {
                            Swal.fire({
                                title: "Poseidon",
                                text: "Confirmar Eliminacion de Registro?",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Si, Confirmo"
                            }).then(async (result) => {
                                if (!result.isConfirmed) {
                                    this.value = "";
                                    return;
                                }

                                try {
                                    const datos = new FormData();
                                    datos.append("idcontrato", contrato.idcontrato);
                                    datos.append("opcion", "eliminar");

                                    const respuesta = await fetch("DAOContrato.php", {
                                        method: "POST",
                                        body: datos
                                    });

                                    if (!respuesta.ok) {
                                        throw new Error("Error de Comunicacion " + respuesta.status);
                                    }

                                    const resultado = await respuesta.json();

                                    if (!resultado.estado) {
                                        throw new Error(resultado.mensaje || "No se pudo eliminar el contrato");
                                    }

                                    Swal.fire({
                                        position: "top-end",
                                        icon: "success",
                                        title: "Poseidon",
                                        text: resultado.mensaje,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {//cuando desaparece el mensaje se recarga la pagina
                                        window.location.href = "lista-contrato.php";
                                    });
                                } catch (error) {
                                    console.error(error);
                                    this.value = "";
                                    await Swal.fire("No se pudo eliminar", error.message, "error");
                                }
                            });
                        }
                    };
                }
            }
        ]
    });



    function actualizarContador() {
        const total = tabla.getDataCount("active");
        contador.textContent = `Total de Registros: ${total}`;
    }

    tabla.on("dataLoaded", actualizarContador);
    tabla.on("dataFiltered", actualizarContador);
    tabla.on("dataChanged", actualizarContador);
    tabla.on("dataFiltered", function (filters, filas) {
        contador.textContent = `Total de Registros: ${filas.length}`;
    });

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

        const respuesta = await fetch("DAOContrato.php", {
            method: "POST",
            body: datos
        });

        if (!respuesta.ok) {
            throw new Error("Error HTTP: " + respuesta.status);
        }

        const contratos = await respuesta.json();
        Swal.close();

        if (!Array.isArray(contratos)) {
            throw new Error(contratos.error || "Respuesta no válida");
        }

        await tabla.replaceData(contratos);
        actualizarContador();

    } catch (error) {
        console.error("Error al cargar registros:", error);
        contenedor.textContent = "No se pudieron cargar los registros.";
    }

    if (filtro) {
        filtro.addEventListener("input", () => {
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
                    { field: "fecha", type: "like", value: valor },
                    { field: "monto", type: "like", value: valor }
                ]
            ]);
        });
    }


    btnImprimir?.addEventListener("click", () => {
        alert("Filtro: " + filtro.value);
    });


});

