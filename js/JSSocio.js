document.addEventListener("DOMContentLoaded", async () => {
    const formulario = document.getElementById("formulario");
    const btnGuardar = document.getElementById("btnGuardar");
    if (!formulario || !btnGuardar) return;

    const dui = document.getElementById("dui");
    const nombre = document.getElementById("nombre");
    const apellido = document.getElementById("apellido");
    const telefono = document.getElementById("telefono");
    const profesion = document.getElementById("profesion");
    const nit = document.getElementById("nit");
    const fecha = document.getElementById("fecha_nacimiento");
    const canton = document.getElementById("idcanton");
    const direccion = document.getElementById("direccion");

    const idsocio = new URLSearchParams(window.location.search).get("id");

    ////////////////// UTILIDADES //////////////////
    function actualizarEstado(campo, valido) {
        campo.classList.toggle("is-invalid", !valido);
        campo.classList.toggle("is-valid", valido);
        return valido;
    }

    function validarCampo(campo) {
        return actualizarEstado(campo, campo.value.trim() !== "");
    }

    function limpiarCampos() {
        formulario.reset();
        formulario.querySelectorAll(".is-valid, .is-invalid").forEach((campo) => {
            campo.classList.remove("is-valid", "is-invalid");
        });
        btnGuardar.textContent = "Guardar";
        delete btnGuardar.dataset.id;
    }

    // Fecha de nacimiento: no se permiten fechas futuras
    function hoy() {
        const d = new Date();
        const mes = String(d.getMonth() + 1).padStart(2, "0");
        const dia = String(d.getDate()).padStart(2, "0");
        return `${d.getFullYear()}-${mes}-${dia}`;
    }
    fecha.max = hoy();

    ////////////////// MASCARAS //////////////////
    // DUI: ########-#
    function normalizarDui(valor) {
        const digitos = valor.replace(/\D/g, "").slice(0, 9);
        return digitos.length > 8 ? `${digitos.slice(0, 8)}-${digitos.slice(8)}` : digitos;
    }
    const validarDui = (valor) => /^\d{8}-\d$/.test(valor);

    // Teléfono: ####-####
    function normalizarTelefono(valor) {
        const digitos = valor.replace(/\D/g, "").slice(0, 8);
        return digitos.length > 4 ? `${digitos.slice(0, 4)}-${digitos.slice(4)}` : digitos;
    }
    const validarTelefono = (valor) => /^\d{4}-\d{4}$/.test(valor);

    // NIT: ####-######-###-#
    function normalizarNit(valor) {
        const d = valor.replace(/\D/g, "").slice(0, 14);
        let formato = d.slice(0, 4);
        if (d.length > 4) formato += "-" + d.slice(4, 10);
        if (d.length > 10) formato += "-" + d.slice(10, 13);
        if (d.length > 13) formato += "-" + d.slice(13);
        return formato;
    }
    const validarNit = (valor) => /^\d{4}-\d{6}-\d{3}-\d$/.test(valor);

    // Texto en mayúsculas
    [nombre, apellido, profesion, direccion].forEach((campo) => {
        campo.addEventListener("input", () => {
            campo.value = campo.value.toLocaleUpperCase();
            validarCampo(campo);
        });
    });

    telefono.addEventListener("input", () => {
        telefono.value = normalizarTelefono(telefono.value);
        actualizarEstado(telefono, validarTelefono(telefono.value));
    });

    nit.addEventListener("input", () => {
        nit.value = normalizarNit(nit.value);
        actualizarEstado(nit, validarNit(nit.value));
    });

    fecha.addEventListener("change", () => {
        actualizarEstado(fecha, fecha.value !== "" && fecha.value <= hoy());
    });

    canton.addEventListener("change", () => validarCampo(canton));

    ////////////////// CARGAR CANTONES //////////////////
    async function cargarCantones() {
        const respuesta = await fetch("DAOCanton.php", {
            method: "POST",
            body: new URLSearchParams({ opcion: "consultar" })
        });
        if (!respuesta.ok) throw new Error(`Error HTTP ${respuesta.status} al cargar los cantones`);

        const cantones = await respuesta.json();
        if (!Array.isArray(cantones)) throw new Error("No se recibió una lista válida de cantones");

        canton.replaceChildren(new Option("[SELECCIONE UN CANTON]", ""));
        cantones.forEach((c) => canton.add(new Option(c.nombre, c.id_canton)));
    }

    ////////////////// LLENAR EL FORMULARIO CON UN SOCIO //////////////////
    function llenarFormulario(socio) {
        dui.value = socio.dui ?? "";
        nombre.value = socio.nombre ?? "";
        apellido.value = socio.apellido ?? "";
        telefono.value = socio.telefono ?? "";
        profesion.value = socio.profesion ?? "";
        nit.value = socio.nit ?? "";
        fecha.value = socio.fecha_nacimiento ?? "";
        canton.value = socio.idcanton ?? "";
        direccion.value = socio.direccion ?? "";

        [dui, nombre, apellido, telefono, profesion, nit, fecha, canton, direccion]
            .forEach((campo) => actualizarEstado(campo, campo.value.trim() !== ""));

        btnGuardar.textContent = "Modificar";
        btnGuardar.dataset.id = socio.idsocio;
    }

    async function consultar(opcion, campo, valor) {
        const datos = new FormData();
        datos.append("opcion", opcion);
        datos.append(campo, valor);

        const respuesta = await fetch("DAOSocio.php", { method: "POST", body: datos });
        if (!respuesta.ok) throw new Error(`Error HTTP ${respuesta.status} al consultar el socio`);
        return await respuesta.json();
    }

    ////////////////// CARGA INICIAL //////////////////
    try {
        await cargarCantones();

        // Si viene ?id=, se esta modificando un socio desde la lista
        if (idsocio) {
            Swal.fire({
                title: "Consultando...",
                text: "Espere",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });
            const resultado = await consultar("consultarIndividual", "idsocio", idsocio);
            Swal.close();
            if (!resultado.estado) throw new Error("No se encontró el socio");
            llenarFormulario(resultado.datos);
        }
    } catch (error) {
        Swal.close();
        console.error(error);
        if (idsocio) btnGuardar.disabled = true;
        await Swal.fire("No se pudo cargar el formulario", error.message, "error");
    }

    ////////////////// DUI: MASCARA Y BUSQUEDA AUTOMATICA //////////////////
    dui.addEventListener("input", async () => {
        dui.value = normalizarDui(dui.value);
        actualizarEstado(dui, validarDui(dui.value));

        // Solo se busca cuando el DUI esta completo y se esta registrando uno nuevo
        if (dui.value.length !== 10 || btnGuardar.dataset.id || idsocio) return;

        try {
            Swal.fire({
                title: "Consultando...",
                text: "Espere",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });
            const resultado = await consultar("consultarDui", "dui", dui.value);
            Swal.close();

            // Si el socio ya existe, se cargan sus datos para modificarlos
            if (resultado.estado) llenarFormulario(resultado.datos);
        } catch (error) {
            Swal.close();
            console.error(error);
        }
    });

    ////////////////// GUARDAR / MODIFICAR //////////////////
    btnGuardar.addEventListener("click", async () => {
        const validos = [
            actualizarEstado(dui, validarDui(dui.value)),
            validarCampo(nombre),
            validarCampo(apellido),
            actualizarEstado(telefono, validarTelefono(telefono.value)),
            validarCampo(profesion),
            actualizarEstado(nit, validarNit(nit.value)),
            actualizarEstado(fecha, fecha.value !== "" && fecha.value <= hoy()),
            validarCampo(canton),
            validarCampo(direccion)
        ];

        if (!validos.every(Boolean)) {
            await Swal.fire("Revisa los datos", "Completa los campos obligatorios y verifica el DUI, teléfono, NIT y la fecha.", "warning");
            return;
        }

        const modificando = btnGuardar.textContent.trim() === "Modificar";

        btnGuardar.disabled = true;
        btnGuardar.textContent = "Procesando...";

        try {
            const datos = new FormData(formulario);
            datos.append("opcion", modificando ? "modificar" : "guardar");
            if (modificando) datos.append("idsocio", btnGuardar.dataset.id);

            const respuesta = await fetch("DAOSocio.php", { method: "POST", body: datos });
            if (!respuesta.ok) throw new Error("Error HTTP: " + respuesta.status);

            const resultado = await respuesta.json();

            if (resultado.estado) {
                await Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: "Poseidón",
                    text: resultado.mensaje,
                    showConfirmButton: false,
                    timer: 1500
                });
                if (modificando && idsocio) {
                    window.location.href = "lista-socio.php";
                    return;
                }
                limpiarCampos();
            } else {
                await Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: "Poseidón",
                    text: resultado.mensaje,
                    showConfirmButton: false,
                    timer: 2000
                });
                btnGuardar.textContent = modificando ? "Modificar" : "Guardar";
            }
        } catch (error) {
            console.error(error);
            await Swal.fire("No se pudo guardar", error.message, "error");
            btnGuardar.textContent = modificando ? "Modificar" : "Guardar";
        } finally {
            btnGuardar.disabled = false;
        }
    });
});
