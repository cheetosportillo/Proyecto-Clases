document.addEventListener("DOMContentLoaded", async () => {
    const formulario = document.getElementById("formulario");
    const btnGuardar = document.getElementById("btnGuardar");
    const btnCancelar = document.getElementById("btnCancelar");
    if (!formulario || !btnGuardar) return;

    const dui = document.getElementById("dui");
    const nombre = document.getElementById("nombre");
    const apellido = document.getElementById("apellido");
    const telefono = document.getElementById("telefono");
    const idempleado = new URLSearchParams(window.location.search).get("id");

    function actualizarEstado(campo, valido) {
        campo.classList.toggle("is-invalid", !valido);
        campo.classList.toggle("is-valid", valido);
        return valido;
    }

    function validarDui(valor) {
        return /^\d{8}-\d$/.test(valor);
    }

    function normalizarDui(valor) {
        const digitos = valor.replace(/\D/g, "").slice(0, 9);
        return digitos.length > 8 ? `${digitos.slice(0, 8)}-${digitos.slice(8)}` : digitos;
    }

    dui.addEventListener("input", () => {
        dui.value = normalizarDui(dui.value);
        actualizarEstado(dui, validarDui(dui.value));
    });

    nombre.addEventListener("input", () => {
        nombre.value = nombre.value.toLocaleUpperCase();
    });
    apellido.addEventListener("input", () => {
        apellido.value = apellido.value.toLocaleUpperCase();
    });
    telefono.addEventListener("input", () => {
        const digitos = telefono.value.replace(/\D/g, "").slice(0, 8);
        telefono.value = digitos.length > 4
            ? `${digitos.slice(0, 4)}-${digitos.slice(4)}`
            : digitos;
    });

    async function cargarOpciones(endpoint, idSelect, idCampo, etiqueta, textoInicial) {
        const respuesta = await fetch(endpoint, {
            method: "POST",
            body: new URLSearchParams({ opcion: "consultar" })
        });
        if (!respuesta.ok) throw new Error(`Error HTTP ${respuesta.status} al cargar ${textoInicial}`);

        const opciones = await respuesta.json();
        if (!Array.isArray(opciones)) {
            throw new Error(`No se recibió una lista válida para ${textoInicial}`);
        }

        const select = document.getElementById(idSelect);
        select.replaceChildren(new Option(textoInicial, ""));
        opciones.forEach((opcion) => {
            select.add(new Option(opcion[etiqueta], opcion[idCampo]));
        });
    }

    async function consultarEmpleado(id) {
        const datos = new FormData();
        datos.append("opcion", "consultarIndividual");
        datos.append("idempleado", id);

        const respuesta = await fetch("DAOEmpleado.php", { method: "POST", body: datos });
        if (!respuesta.ok) throw new Error(`Error HTTP ${respuesta.status} al consultar el empleado`);

        const resultado = await respuesta.json();
        if (!resultado.estado) throw new Error(resultado.datos || "No se encontró el empleado");

        const empleado = resultado.datos;
        ["dui", "nombre", "apellido", "telefono", "num_isss", "id_afp", "nup",
            "sueldo", "id_renta", "fecha_ingreso", "id_cargo", "id_canton", "id_estado"]
            .forEach((campo) => {
                document.getElementById(campo).value = empleado[campo] ?? "";
            });
        btnGuardar.textContent = "Modificar";
        btnGuardar.dataset.id = empleado.idempleado;
    }

    async function cargarCatalogos() {
        const respuesta = await fetch("DAOEmpleado.php", {
            method: "POST",
            body: new URLSearchParams({ opcion: "consultarCatalogos" })
        });
        if (!respuesta.ok) throw new Error(`Error HTTP ${respuesta.status} al cargar AFP, renta y estados`);

        const resultado = await respuesta.json();
        if (resultado.estado === false) {
            throw new Error(resultado.mensaje || "No se pudieron cargar AFP, renta y estados");
        }

        [
            ["afp", "id_afp", "Seleccione AFP"],
            ["renta", "id_renta", "Seleccione renta"],
            ["estado", "id_estado", "Seleccione estado"]
        ].forEach(([catalogo, idSelect, textoInicial]) => {
            const select = document.getElementById(idSelect);
            select.replaceChildren(new Option(`[${textoInicial.toUpperCase()}]`, ""));
            (resultado[catalogo] || []).forEach((opcion) => {
                select.add(new Option(opcion.nombre, opcion.id));
            });
        });
    }

    try {
        await Promise.all([
            cargarOpciones("DAOCargo.php", "id_cargo", "id_cargo", "nombre", "[SELECCIONE UN CARGO]"),
            cargarOpciones("DAOCanton.php", "id_canton", "id_canton", "nombre", "[SELECCIONE UN CANTON]"),
            cargarCatalogos()
        ]);
        if (idempleado) await consultarEmpleado(idempleado);
    } catch (error) {
        console.error(error);
        if (idempleado) btnGuardar.disabled = true;
        await Swal.fire("No se pudo cargar el formulario", error.message, "error");
    }

    async function consultarDuiDisponible() {
        if (!validarDui(dui.value)) {
            actualizarEstado(dui, false);
            return false;
        }

        const datos = new FormData();
        datos.append("opcion", "consultarDUI");
        datos.append("dui", dui.value);
        if (btnGuardar.dataset.id) datos.append("idempleado", btnGuardar.dataset.id);

        const respuesta = await fetch("DAOEmpleado.php", { method: "POST", body: datos });
        if (!respuesta.ok) throw new Error(`Error HTTP ${respuesta.status} al validar el DUI`);
        const resultado = await respuesta.json();
        actualizarEstado(dui, !resultado.estado);
        if (resultado.estado) await Swal.fire("DUI duplicado", resultado.mensaje, "warning");
        return !resultado.estado;
    }

    dui.addEventListener("blur", async () => {
        if (!dui.value) return;
        try {
            await consultarDuiDisponible();
        } catch (error) {
            console.error(error);
            await Swal.fire("Error al validar el DUI", error.message, "error");
        }
    });

    btnCancelar.addEventListener("click", () => {
        if (idempleado) {
            window.location.href = "lista-empleados.php";
            return;
        }

        formulario.reset();
        formulario.querySelectorAll(".is-valid, .is-invalid").forEach((campo) => {
            campo.classList.remove("is-valid", "is-invalid");
        });
    });

    btnGuardar.addEventListener("click", async () => {
        const requeridosValidos = [...formulario.querySelectorAll("[required]")].every((campo) => {
            const valido = campo.value.trim() !== "" && campo.checkValidity();
            return actualizarEstado(campo, valido);
        });
        const duiValido = actualizarEstado(dui, validarDui(dui.value));
        const telefonoValido = actualizarEstado(
            telefono,
            /^\d{4}-\d{4}$/.test(telefono.value)
        );

        if (!requeridosValidos || !duiValido || !telefonoValido) {
            await Swal.fire("Revisa los datos", "Completa los campos obligatorios y verifica el DUI y teléfono.", "warning");
            return;
        }

        btnGuardar.disabled = true;
        try {
            if (!await consultarDuiDisponible()) return;

            const datos = new FormData(formulario);
            datos.append("opcion", btnGuardar.dataset.id ? "modificar" : "guardar");
            if (btnGuardar.dataset.id) datos.append("idempleado", btnGuardar.dataset.id);

            const respuesta = await fetch("DAOEmpleado.php", { method: "POST", body: datos });
            if (!respuesta.ok) throw new Error(`Error HTTP ${respuesta.status} al guardar el empleado`);

            const resultado = await respuesta.json();
            if (!resultado.estado) throw new Error(resultado.mensaje || "No se pudo guardar el empleado");

            await Swal.fire({
                icon: "success",
                title: "Poseidón",
                text: resultado.mensaje,
                timer: 1500,
                showConfirmButton: false
            });
            window.location.href = "lista-empleados.php";
        } catch (error) {
            console.error(error);
            await Swal.fire("No se pudo guardar", error.message, "error");
        } finally {
            btnGuardar.disabled = false;
        }
    });
});
