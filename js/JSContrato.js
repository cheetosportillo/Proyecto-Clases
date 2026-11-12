document.addEventListener("DOMContentLoaded", async () => {
    const formulario = document.getElementById("formulario");
    const btnGuardar = document.getElementById("btnGuardar");
    const btnCancelar = document.getElementById("btnCancelar");
    if (!formulario || !btnGuardar) return;

    const dui = document.getElementById("dui");
    const nombre = document.getElementById("nombre");
    const empleado1 = document.getElementById("empleado1");
    const empleado2 = document.getElementById("empleado2");
    const cargo1 = document.getElementById("cargo1");
    const cargo2 = document.getElementById("cargo2");
    const numero = document.getElementById("numero");
    const fecha = document.getElementById("fecha");
    const monto = document.getElementById("monto");

    // idsocio del socio encontrado por DUI (o del contrato que se modifica)
    let idsocio = "";
    const idcontrato = new URLSearchParams(window.location.search).get("id");

    ////////////////// UTILIDADES //////////////////
    function actualizarEstado(campo, valido) {
        campo.classList.toggle("is-invalid", !valido);
        campo.classList.toggle("is-valid", valido);
        return valido;
    }

    function validarCampo(campo) {
        return actualizarEstado(campo, campo.value.trim() !== "");
    }

    function validarSelect(select) {
        return actualizarEstado(select, select.value !== "0" && select.value !== "");
    }

    function hoy() {
        const d = new Date();
        const mes = String(d.getMonth() + 1).padStart(2, "0");
        const dia = String(d.getDate()).padStart(2, "0");
        return `${d.getFullYear()}-${mes}-${dia}`;
    }

    function limpiarFormulario() {
        formulario.reset();
        formulario.querySelectorAll(".is-valid, .is-invalid").forEach((campo) => {
            campo.classList.remove("is-valid", "is-invalid");
        });
        idsocio = "";
        fecha.value = hoy();
        btnGuardar.textContent = "Guardar";
        btnGuardar.disabled = true;
        dui.disabled = false;
    }

    // La fecha del contrato empieza en hoy y no puede ser futura
    fecha.value = hoy();
    fecha.max = hoy();
    fecha.addEventListener("change", () => {
        if (fecha.value > hoy()) {
            Swal.fire({
                icon: "warning",
                title: "Fecha inválida",
                text: "No puedes seleccionar una fecha futura."
            });
            fecha.value = hoy();
        }
        actualizarEstado(fecha, fecha.value !== "");
    });

    ////////////////// MASCARAS //////////////////
    // DUI: ########-#
    function normalizarDui(valor) {
        const digitos = valor.replace(/\D/g, "").slice(0, 9);
        return digitos.length > 8 ? `${digitos.slice(0, 8)}-${digitos.slice(8)}` : digitos;
    }
    const validarDui = (valor) => /^\d{8}-\d$/.test(valor);

    // Monto: solo numeros y un punto decimal (maximo 2 decimales)
    monto.addEventListener("input", () => {
        let valor = monto.value.replace(/[^0-9.]/g, "");
        const partes = valor.split(".");
        if (partes.length > 2) valor = partes[0] + "." + partes.slice(1).join("");
        const posicion = valor.indexOf(".");
        if (posicion !== -1) valor = valor.substring(0, posicion + 3);
        monto.value = valor;
        actualizarEstado(monto, monto.value !== "" && parseFloat(monto.value) > 0);
    });

    // Cargos en mayusculas
    [cargo1, cargo2].forEach((campo) => {
        campo.addEventListener("input", () => {
            campo.value = campo.value.toLocaleUpperCase();
            if (campo === cargo1 || empleado2.value !== "0") validarCampo(campo);
        });
    });

    empleado1.addEventListener("change", () => validarSelect(empleado1));
    empleado2.addEventListener("change", () => {
        if (empleado2.value === "0") {
            cargo2.value = "";
            [empleado2, cargo2].forEach((c) => c.classList.remove("is-valid", "is-invalid"));
        } else {
            validarSelect(empleado2);
        }
    });

    ////////////////// CARGAR EMPLEADOS EN LOS DOS SELECT //////////////////
    async function cargarEmpleados() {
        const respuesta = await fetch("DAOEmpleado.php", {
            method: "POST",
            body: new URLSearchParams({ opcion: "consultar" })
        });
        if (!respuesta.ok) throw new Error(`Error HTTP ${respuesta.status} al cargar los empleados`);

        const empleados = await respuesta.json();
        if (!Array.isArray(empleados)) throw new Error("No se recibió una lista válida de empleados");

        [empleado1, empleado2].forEach((select) => {
            select.replaceChildren(new Option("[SELECCIONE]", "0"));
            empleados.forEach((e) => {
                select.add(new Option(`${e.nombre} ${e.apellido}`, e.idempleado));
            });
        });
    }

    async function llamarDAO(datos) {
        const respuesta = await fetch("DAOContrato.php", { method: "POST", body: datos });
        if (!respuesta.ok) throw new Error("Error HTTP: " + respuesta.status);
        return await respuesta.json();
    }

    function mostrarEspera(titulo) {
        Swal.fire({
            title: titulo,
            text: "Espere",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });
    }

    ////////////////// BUSQUEDA DE DUI (SE BUSCA AL COMPLETAR EL DUI) //////////////////
    dui.addEventListener("input", async () => {
        dui.value = normalizarDui(dui.value);
        actualizarEstado(dui, validarDui(dui.value));

        if (dui.value.length !== 10) {
            // DUI incompleto: se limpia el socio y no se puede guardar
            nombre.value = "";
            idsocio = "";
            nombre.classList.remove("is-valid", "is-invalid");
            if (!idcontrato) btnGuardar.disabled = true;
            return;
        }

        try {
            mostrarEspera("Consultando DUI...");

            const datos = new FormData();
            datos.append("opcion", "consultarDUI");
            datos.append("dui", dui.value);

            const resultado = await llamarDAO(datos);
            Swal.close();

            if (resultado.estado) {
                nombre.value = resultado.datos.nombre + " " + resultado.datos.apellido;
                idsocio = resultado.datos.idsocio;
                actualizarEstado(nombre, true);
                btnGuardar.disabled = false;
            } else {
                nombre.value = "";
                idsocio = "";
                actualizarEstado(nombre, false);
                btnGuardar.disabled = true;
                Swal.fire({
                    icon: "warning",
                    title: "Poseidón",
                    text: "No existe un socio con ese DUI."
                });
            }
        } catch (error) {
            Swal.close();
            console.error(error);
            Swal.fire("Error al consultar el DUI", error.message, "error");
        }
    });

    ////////////////// CANCELAR //////////////////
    btnCancelar.addEventListener("click", () => {
        window.location.href = "contrato.php";
    });

    ////////////////// GUARDAR / MODIFICAR //////////////////
    btnGuardar.addEventListener("click", async () => {
        const duiValido = actualizarEstado(dui, validarDui(dui.value));
        const nombreValido = validarCampo(nombre);
        const empleado1Valido = validarSelect(empleado1);
        const cargo1Valido = validarCampo(cargo1);
        const montoValido = actualizarEstado(monto, monto.value !== "" && parseFloat(monto.value) > 0);
        const fechaValida = actualizarEstado(fecha, fecha.value !== "" && fecha.value <= hoy());

        // El segundo testigo es opcional, pero si se elige debe llevar su cargo
        let segundoValido = true;
        if (empleado2.value !== "0") {
            segundoValido = validarCampo(cargo2);
            if (empleado2.value === empleado1.value) {
                segundoValido = false;
                actualizarEstado(empleado2, false);
                await Swal.fire("Revisa los datos", "Los dos empleados no pueden ser la misma persona.", "warning");
            }
        }

        if (!(duiValido && nombreValido && empleado1Valido && cargo1Valido && montoValido && fechaValida && segundoValido)) {
            return;
        }

        const modificando = btnGuardar.textContent.trim() === "Modificar";

        try {
            const datos = new FormData(formulario);
            datos.set("dui", dui.value);   // el DUI se envia aunque este deshabilitado
            datos.append("opcion", modificando ? "modificar" : "guardar");
            if (modificando) datos.append("idcontrato", btnGuardar.dataset.id);
            else datos.append("idsocio", idsocio);

            btnGuardar.disabled = true;
            btnGuardar.textContent = "Procesando...";

            const resultado = await llamarDAO(datos);

            if (resultado.estado) {
                await Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: "Poseidón",
                    text: resultado.mensaje,
                    showConfirmButton: false,
                    timer: 1500
                });
                if (modificando) {
                    window.location.href = "lista-contrato.php";
                    return;
                }
                limpiarFormulario();
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
                btnGuardar.disabled = false;
            }
        } catch (error) {
            console.error(error);
            await Swal.fire("No se pudo guardar", error.message, "error");
            btnGuardar.textContent = modificando ? "Modificar" : "Guardar";
            btnGuardar.disabled = false;
        }
    });

    ////////////////// CARGA INICIAL //////////////////
    try {
        await cargarEmpleados();
    } catch (error) {
        console.error(error);
        await Swal.fire("No se pudieron cargar los empleados", error.message, "error");
    }

    // Si viene ?id= se esta modificando un contrato desde la lista
    if (idcontrato) {
        try {
            mostrarEspera("Consultando...");

            const datos = new FormData();
            datos.append("opcion", "consultarIndividual");
            datos.append("idcontrato", idcontrato);

            const resultado = await llamarDAO(datos);
            Swal.close();

            if (!resultado.estado) throw new Error("No se encontró el contrato");

            const c = resultado.datos;
            dui.value = c.dui;
            nombre.value = c.nombre + " " + c.apellido;
            empleado1.value = c.idempleado1;
            empleado2.value = c.idempleado2;
            cargo1.value = c.cargo1;
            cargo2.value = c.cargo2;
            monto.value = c.monto;
            numero.value = c.ncontrato;
            fecha.value = c.fecha;
            idsocio = c.idsocio;

            btnGuardar.textContent = "Modificar";
            btnGuardar.dataset.id = c.idcontrato;
            btnGuardar.disabled = false;

            // El socio de un contrato existente no se puede cambiar
            dui.disabled = true;
        } catch (error) {
            Swal.close();
            console.error(error);
            await Swal.fire("No se pudo cargar el contrato", error.message, "error");
        }
    }
});
