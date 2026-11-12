function validarCampo(campo) {
    const vacio = campo.value.trim() === "" || campo.value === "0";
    campo.classList.toggle("is-invalid", vacio);
    campo.classList.toggle("is-valid", !vacio);
    return !vacio;
}

const formulario = document.getElementById("formulario");
const btnGuardar = document.getElementById("btnGuardar");
const btnEliminar = document.getElementById("btnEliminar");
const btnCancelar = document.getElementById("btnCancelar");
const empleado = document.getElementById("empleado");
const ruta = document.getElementById("ruta");
const rutas = document.getElementById("rutas");

const campos = [empleado, ruta];

/////////////////// UTILIDADES ///////////////////
async function llamarDAO(datos) {
    const respuesta = await fetch("DAOAsignarRuta.php", {
        method: "POST",
        body: datos
    });
    if (!respuesta.ok) {
        throw new Error("Error HTTP: " + respuesta.status);
    }
    return await respuesta.json();
}

function llenarSelect(select, textoInicial, valorInicial, lista, campoValor, campoTexto) {
    select.innerHTML = `<option value="${valorInicial}">${textoInicial}</option>`;
    lista.forEach(function (item) {
        const option = document.createElement("option");
        option.value = item[campoValor];
        option.textContent = item[campoTexto];
        select.appendChild(option);
    });
}

function mostrarMensaje(icono, texto, alCerrar) {
    Swal.fire({
        position: "top-end",
        icon: icono,
        title: "Poseidón",
        text: texto,
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        if (alCerrar) alCerrar();
    });
}

/////////////////// CARGA DE RUTAS ACTUALES DEL EMPLEADO ///////////////////
async function cargarRutasActuales(idEmpleado) {
    // Se limpia el select y se oculta el boton Eliminar
    llenarSelect(rutas, "[RUTAS ACTUALES]", "0", [], "", "");
    btnEliminar.style.display = "none";

    if (!idEmpleado) return;

    try {
        const datos = new FormData();
        datos.append("opcion", "consultarRutas");
        datos.append("empleado", idEmpleado);

        const resultado = await llamarDAO(datos);

        if (resultado.estado) {
            llenarSelect(rutas, "[RUTAS ACTUALES]", "0", resultado.datos, "idempleado_ruta", "nombre");
        }
    } catch (error) {
        console.error("Error al consultar las rutas del empleado:", error);
    }
}

if (formulario) {
    ////////////////////VALIDACION DE CAMPOS VACIOS
    campos.forEach(function (campo) {
        campo.addEventListener("blur", function () {
            validarCampo(campo);
        });
    });

    ////////////////////AL ELEGIR EMPLEADO SE MUESTRAN SUS RUTAS ACTUALES
    empleado.addEventListener("change", function () {
        validarCampo(empleado);
        cargarRutasActuales(empleado.value);
    });

    ruta.addEventListener("change", function () {
        validarCampo(ruta);
    });

    ////////////////////EL BOTON ELIMINAR SOLO SE VE SI HAY UNA RUTA ACTUAL SELECCIONADA
    rutas.addEventListener("change", function () {
        btnEliminar.style.display = rutas.value !== "0" ? "inline-block" : "none";
    });

    ////////////////////GUARDAR
    btnGuardar.addEventListener("click", async function () {
        const empleadoValido = validarCampo(empleado);
        const rutaValida = validarCampo(ruta);

        if (!(empleadoValido && rutaValida)) return;

        try {
            const datos = new FormData(formulario);
            datos.append("opcion", "guardar");

            btnGuardar.disabled = true;
            btnGuardar.textContent = "Procesando...";

            const resultado = await llamarDAO(datos);

            if (resultado.estado) {
                mostrarMensaje("success", resultado.mensaje, function () {
                    window.location.href = "asignarRuta.php";
                });
            } else {
                mostrarMensaje("error", resultado.mensaje);
            }
        } catch (error) {
            console.error(error);
            mostrarMensaje("error", "No se pudo guardar, intente de nuevo");
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.textContent = "Guardar";
        }
    });

    ////////////////////ELIMINAR LA RUTA ACTUAL SELECCIONADA
    btnEliminar.addEventListener("click", function () {
        if (rutas.value === "0") return;

        Swal.fire({
            title: "Poseidón",
            text: "¿Confirmar eliminación de la ruta asignada?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, Confirmo",
            cancelButtonText: "Cancelar"
        }).then(async (result) => {
            if (!result.isConfirmed) return;

            try {
                const datos = new FormData();
                datos.append("opcion", "eliminar");
                datos.append("idempleado_ruta", rutas.value);

                const resultado = await llamarDAO(datos);

                if (resultado.estado) {
                    mostrarMensaje("success", resultado.mensaje, function () {
                        window.location.href = "asignarRuta.php";
                    });
                } else {
                    mostrarMensaje("error", resultado.mensaje);
                }
            } catch (error) {
                console.error(error);
                mostrarMensaje("error", "No se pudo eliminar, intente de nuevo");
            }
        });
    });

    ////////////////////CANCELAR
    btnCancelar.addEventListener("click", function () {
        window.location.href = "asignarRuta.php";
    });
}

//////////PROCESA AL TERMINAR DE CARGAR LA PAGINA
document.addEventListener("DOMContentLoaded", async () => {
    if (!formulario) return;

    try {
        // Todos los empleados registrados
        const datosEmp = new FormData();
        datosEmp.append("opcion", "consultarLectores");
        const empleados = await llamarDAO(datosEmp);
        if (!Array.isArray(empleados)) {
            throw new Error(empleados.mensaje || "No se pudo cargar la lista de empleados");
        }
        llenarSelect(empleado, "[SELECCIONE EMPLEADO]", "", empleados, "idempleado", "nombre");

        // Rutas disponibles
        const datosRuta = new FormData();
        datosRuta.append("opcion", "consultarRutasDisponibles");
        const listaRutas = await llamarDAO(datosRuta);
        if (!Array.isArray(listaRutas)) {
            throw new Error(listaRutas.mensaje || "No se pudo cargar la lista de rutas");
        }
        llenarSelect(ruta, "[SELECCIONE RUTA]", "", listaRutas, "idruta", "nombre");
    } catch (error) {
        console.error("Error al cargar los datos iniciales:", error);
        await Swal.fire("No se pudieron cargar los datos", error.message, "error");
    }
});
