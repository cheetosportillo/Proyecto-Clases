document.addEventListener("DOMContentLoaded", async () => {
    const prefijo = document.getElementById("prefijo");
    const ultimo = document.getElementById("ultimo");
    const estado = document.getElementById("estado");
    const btnNuevo = document.getElementById("btnNuevo");
    const btnCancelar = document.getElementById("btnCancelar");
    let tirajes = [];

    async function solicitar(datos) {
        const respuesta = await fetch("DAOTiraje.php", { method: "POST", body: datos });
        const resultado = await respuesta.json();
        if (!respuesta.ok) {
            throw new Error(resultado.mensaje || `Error HTTP ${respuesta.status}`);
        }
        return resultado;
    }

    function mostrarTiraje(valor) {
        const tiraje = tirajes.find((registro) => String(registro.prefijo) === String(valor));
        ultimo.value = tiraje ? tiraje.ultimo : "";
        estado.value = tiraje ? tiraje.estado : "";
    }

    async function cargarTirajes(prefijoSeleccionado = null) {
        const datos = new FormData();
        datos.append("opcion", "consultar");
        const resultado = await solicitar(datos);

        if (!Array.isArray(resultado)) {
            throw new Error(resultado.mensaje || "No se recibió una lista válida de periodos");
        }

        tirajes = resultado;
        prefijo.replaceChildren(new Option("[Seleccione]", ""));
        tirajes.forEach((tiraje) => {
            prefijo.add(new Option(tiraje.prefijo, tiraje.prefijo));
        });

        const activo = tirajes.find((tiraje) => tiraje.estado === "Activo");
        const seleccionado = prefijoSeleccionado ?? (activo ? activo.prefijo : tirajes[0]?.prefijo);
        prefijo.value = seleccionado == null ? "" : String(seleccionado);
        mostrarTiraje(prefijo.value);
    }

    prefijo.addEventListener("change", () => mostrarTiraje(prefijo.value));

    btnNuevo.addEventListener("click", async () => {
        const confirmacion = await Swal.fire({
            title: "Crear nuevo tiraje",
            text: "El periodo activo pasará a inactivo y el nuevo periodo comenzará en cero.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Crear tiraje",
            cancelButtonText: "Cancelar"
        });

        if (!confirmacion.isConfirmed) return;

        btnNuevo.disabled = true;
        try {
            const datos = new FormData();
            datos.append("opcion", "crearNuevo");
            const resultado = await solicitar(datos);

            if (!resultado.estado || !resultado.datos) {
                throw new Error(resultado.mensaje || "No se pudo crear el tiraje");
            }

            tirajes = tirajes.map((tiraje) => ({
                ...tiraje,
                estado: tiraje.estado === "Activo" ? "Inactivo" : tiraje.estado
            }));
            tirajes.unshift(resultado.datos);
            prefijo.replaceChildren(new Option("[Seleccione]", ""));
            tirajes.forEach((tiraje) => {
                prefijo.add(new Option(tiraje.prefijo, tiraje.prefijo));
            });
            prefijo.value = String(resultado.datos.prefijo);
            mostrarTiraje(prefijo.value);

            await Swal.fire("Tiraje creado", resultado.mensaje, "success");
        } catch (error) {
            console.error(error);
            await Swal.fire("No se pudo crear el tiraje", error.message, "error");
        } finally {
            btnNuevo.disabled = false;
        }
    });

    btnCancelar.addEventListener("click", () => {
        const activo = tirajes.find((tiraje) => tiraje.estado === "Activo");
        prefijo.value = activo ? String(activo.prefijo) : "";
        mostrarTiraje(prefijo.value);
    });

    try {
        await cargarTirajes();
    } catch (error) {
        console.error(error);
        await Swal.fire("No se pudo cargar la configuración", error.message, "error");
        btnNuevo.disabled = true;
    }
});
