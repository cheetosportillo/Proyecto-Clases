
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
            { title: "Full Name", field: "nombreCompleto" },
            { title: "Telefono", field: "telefono" },
            { title: "Tamanio", field: "tamanio" },
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


                                    if (resultado.estado) {
                                        Swal.fire({
                                            position: "top-end",
                                            icon: "success",
                                            title: "Poseidon",
                                            text: resultado.mensaje,
                                            showConfirmButton: false,
                                            timer: 1500
                                        }).then(() => {//cuando desaparece el mensaje se recarga la pagina
                                            window.location.href = "lista-empleados.php";
                                        });
                                    }
                                } catch (error) {
                                }
                            }
                        });
                    }
                }
            },
            {
                title: "Acciones", field: "acciones", hozAlign: "center", headerSort: false,
                formatter: function () {
                    return `
                        <select class="form-select form-select-sm accion-empleado">
                            <option value="">Seleccionar...</option>
                            <option value="modificar">Modificar</option>
                            <option value="eliminar">Eliminar</option>
                        </select>
                    `;
                },
                cellEdited: function (cell) {
                    console.log(cell.getValue());
                },
            
                cellClick: function (e, cell) {
                    if (e.target.matches(".accion-empleado")) {
                        e.target.onchange = function () {
                            const accion = this.value;
                            const empleado = cell.getRow().getData();
            
                            if (accion === "modificar") {
                                location.href = "empleado.php?id=" + empleado.idempleado;
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
        
        
                                            if (resultado.estado) {
                                                Swal.fire({
                                                    position: "top-end",
                                                    icon: "success",
                                                    title: "Poseidon",
                                                    text: resultado.mensaje,
                                                    showConfirmButton: false,
                                                    timer: 1500
                                                }).then(() => {//cuando desaparece el mensaje se recarga la pagina
                                                    window.location.href = "lista-empleados.php";
                                                });
                                            }
                                        } catch (error) {
                                        }
                                    }
                                });
                            }
            
                            //this.value = "";
                        };
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

    contenedor.addEventListener("change", function (e) {
        if (!e.target.matches("select.accion-empleado")) return;

        const accion = e.target.value;
        const fila = tabla.getRow(e.target.closest(".tabulator-row"));

        if (!fila || !accion) return;

        const empleado = fila.getData();

        if (accion === "modificar") {
            location.href = "empleado.php?id=" + empleado.idempleado;
        } else if (accion === "eliminar") {
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


                        if (resultado.estado) {
                            Swal.fire({
                                position: "top-end",
                                icon: "success",
                                title: "Poseidon",
                                text: resultado.mensaje,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {//cuando desaparece el mensaje se recarga la pagina
                                window.location.href = "lista-empleados.php";
                            });
                        }
                    } catch (error) {
                    }
                }
            });

        }
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
                    { field: "nombreCompleto", type: "like", value: valor },
                    { field: "tamanio", type: "like", value: valor }
                ]
            ]);
        });
    }


    btnImprimir?.addEventListener("click", () => {
        alert("Filtro: " + filtro.value);
    });


});






