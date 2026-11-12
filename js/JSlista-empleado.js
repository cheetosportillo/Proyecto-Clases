
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
            { title: "ID", field: "idempleado" },
            { title: "DUI", field: "dui" },
            { title: "Nombre", field: "nombre" },
            { title: "Apellido", field: "apellido" },
            { title: "Teléfono", field: "telefono" },
            { title: "Sueldo", field: "sueldo" },
            { title: "AFP", field: "afp" },
            { title: "NUP", field: "nup" },
            { title: "Cargo", field: "cargo" },
            { title: "Fecha de ingreso", field: "fecha_ingreso" },
            { title: "Renta", field: "renta" },
            { title: "Cantón", field: "canton" },
            { title: "ISSS", field: "num_isss" },
            { title: "Estado", field: "estado" },
            {
                title: "Acciones", field: "acciones", hozAlign: "center", headerSort: false,
                formatter: function () {
                    return `
                    <button type="button" class="btn btn-outline-secondary btn-editar">
                        <i class="bi bi-pencil" aria-hidden="true"> </i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-eliminar">
                        <i class="bi bi-trash" aria-hidden="true"> </i>
                    </button>
                    `;
                },
                cellClick: function (e, cell) {
                    const empleado = cell.getRow().getData();

                    if (e.target.closest(".btn-editar")) {
                        location.href = "empleado.php?id=" + empleado.idempleado;
                    }

                    if (e.target.closest(".btn-eliminar")) {
                        Swal.fire({
                            title: "Poseidon",
                            text: "Confirmar Eliminacion de Registro?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Si, Confirmo"
                        }).then(async (result) => {
                            if (result.isConfirmed) {
                                try {

                                    const datos = new FormData();

                                    datos.append("idempleado", empleado.idempleado);
                                    datos.append("opcion", "eliminar");

                                    const respuesta = await fetch("DAOEmpleado.php", {
                                        method: "POST",
                                        body: datos
                                    });

                                    //verificar respuesta

                                    if (!respuesta.ok) {
                                        throw new Error("Error de Comunicacion" + respuesta.status);
                                    }

                                    const resultado = await respuesta.json();

                                    if (!resultado.estado) {
                                        throw new Error(resultado.mensaje || "No se pudo eliminar el empleado");
                                    }

                                    Swal.fire({
                                        position: "top-end",
                                        icon: "success",
                                        title: "Poseidon",
                                        text: resultado.mensaje,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        window.location.href = "lista-empleados.php";
                                    });
                                } catch (error) {
                                    console.error(error);
                                    await Swal.fire("No se pudo eliminar", error.message, "error");
                                }
                            }
                        });
                    }
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

        const respuesta = await fetch("DAOEmpleado.php", {
            method: "POST",
            body: datos
        });

        if (!respuesta.ok) {
            throw new Error("Error HTTP: " + respuesta.status);
        }

        const empleados = await respuesta.json();
        Swal.close();

        if (!Array.isArray(empleados)) {
            throw new Error(empleados.error || "Respuesta no válida");
        }

        await tabla.replaceData(empleados);
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
                    { field: "telefono", type: "like", value: valor },
                    { field: "sueldo", type: "like", value: valor },
                    { field: "afp", type: "like", value: valor },
                    { field: "nup", type: "like", value: valor },
                    { field: "cargo", type: "like", value: valor },
                    { field: "fecha_ingreso", type: "like", value: valor },
                    { field: "renta", type: "like", value: valor },
                    { field: "canton", type: "like", value: valor },
                    { field: "num_isss", type: "like", value: valor },
                    { field: "estado", type: "like", value: valor }
                ]
            ]);
        });
    }


    btnImprimir?.addEventListener("click", () => {
        alert("Filtro: " + filtro.value);
    });


});

