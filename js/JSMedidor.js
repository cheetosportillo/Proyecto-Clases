document.addEventListener("DOMContentLoaded", async () => {
    const campos = {
        dui: document.getElementById("dui"),
        nombre: document.getElementById("nombre"),
        numero: document.getElementById("numero"),
        fecha: document.getElementById("fecha"),
        canton: document.getElementById("canton"),
        ruta: document.getElementById("ruta"),
        direccion: document.getElementById("direccion"),
        referencia: document.getElementById("referencia"),
        nmedidor: document.getElementById("nmedidor"),
        inicio: document.getElementById("inicio"),
        estado: document.getElementById("estado")
    };
    const formulario = document.getElementById("formulario");
    const btnGuardar = document.getElementById("btnGuardar");
    const btnCancelar = document.getElementById("btnCancelar");

    async function consultar(url, datos, descripcion) {
        const respuesta = await fetch(url, { method: "POST", body: datos });
        const resultado = await respuesta.json();
        if (!respuesta.ok) {
            throw new Error(resultado.mensaje || `Error HTTP ${respuesta.status} al ${descripcion}`);
        }
        return resultado;
    }

    async function cargarOpciones(url, select, idCampo) {
        const datos = new FormData();
        datos.append("opcion", "consultar");
        const opciones = await consultar(url, datos, "cargar opciones");

        if (!Array.isArray(opciones)) {
            throw new Error("No se recibió una lista válida para los selectores");
        }

        select.replaceChildren(new Option("[SELECCIONE]", "0"));
        opciones.forEach((opcion) => {
            select.add(new Option(opcion.nombre, opcion[idCampo]));
        });
    }

    btnGuardar.addEventListener("click", async () => {
        if (!formulario.reportValidity()) return;

        if (campos.canton.value === "0" || campos.ruta.value === "0" || campos.estado.value === "0") {
            await Swal.fire("Revisa los datos", "Selecciona el cantón, la ruta y el estado del medidor.", "warning");
            return;
        }

        const modificando = Boolean(btnGuardar.dataset.idmedidor);
        const lecturaInicial = Number(campos.inicio.value);
        if (!Number.isFinite(lecturaInicial) || lecturaInicial < 0) {
            await Swal.fire("Lectura inválida", "Los metros iniciales deben ser un número igual o mayor a cero.", "warning");
            return;
        }

        const datos = new FormData();
        datos.append("opcion", modificando ? "modificar" : "guardar");
        datos.append("idcontrato", btnGuardar.dataset.idcontrato);
        datos.append("nmedidor", campos.nmedidor.value.trim());
        datos.append("inicio", campos.inicio.value);
        datos.append("canton", campos.canton.value);
        datos.append("ruta", campos.ruta.value);
        datos.append("direccion", campos.direccion.value.trim());
        datos.append("referencia", campos.referencia.value.trim());
        datos.append("estado", campos.estado.value);
        if (modificando) datos.append("idmedidor", btnGuardar.dataset.idmedidor);

        btnGuardar.disabled = true;
        btnCancelar.disabled = true;
        btnGuardar.textContent = "Procesando...";

        try {
            const resultado = await consultar("DAOMedidor.php", datos, "guardar el medidor");
            if (!resultado.estado) {
                throw new Error(resultado.mensaje || "No se pudieron guardar los datos del medidor");
            }

            await Swal.fire("Poseidón", resultado.mensaje, "success");
            window.location.href = "lista-medidores.php";
        } catch (error) {
            console.error(error);
            await Swal.fire("No se pudo guardar", error.message, "error");
            btnGuardar.disabled = false;
            btnCancelar.disabled = false;
            btnGuardar.textContent = modificando ? "Modificar" : "Guardar";
        }
    });

    btnCancelar.addEventListener("click", () => {
        window.location.href = "lista-medidores.php";
    });

    try {
        await Promise.all([
            cargarOpciones("DAOCanton.php", campos.canton, "id_canton"),
            cargarOpciones("DAORuta.php", campos.ruta, "idRuta")
        ]);

        const idcontrato = new URLSearchParams(window.location.search).get("id");
        if (!idcontrato) {
            throw new Error("No se indicó el contrato para asignar el medidor");
        }

        const datos = new FormData();
        datos.append("opcion", "consultarMedidor");
        datos.append("idcontrato", idcontrato);

        const resultado = await consultar("DAOContrato.php", datos, "consultar el contrato");
        if (!resultado.estado || !resultado.datos) {
            throw new Error("No se encontró el contrato");
        }

        const medidor = resultado.datos;
        campos.dui.value = medidor.dui ?? "";
        campos.nombre.value = `${medidor.nombre ?? ""} ${medidor.apellido ?? ""}`.trim();
        campos.numero.value = medidor.ncontrato ?? "";
        campos.fecha.value = medidor.fecha ?? "";
        campos.canton.value = medidor.idcanton ?? "0";
        campos.ruta.value = medidor.idruta ?? "0";
        campos.direccion.value = medidor.direccion ?? "";
        campos.referencia.value = medidor.referencia ?? "";
        campos.nmedidor.value = medidor.nmedidor ?? "";
        campos.inicio.value = medidor.inicio ?? "";
        campos.estado.value = medidor.estado ?? "Activo";

        btnGuardar.textContent = medidor.idmedidor ? "Modificar" : "Guardar";
        btnGuardar.dataset.idcontrato = idcontrato;
        if (medidor.idmedidor) {
            btnGuardar.dataset.idmedidor = medidor.idmedidor;
        } else {
            delete btnGuardar.dataset.idmedidor;
        }
        btnGuardar.disabled = false;
    } catch (error) {
        console.error(error);
        await Swal.fire("No se pudo cargar el medidor", error.message, "error");
    }
});
