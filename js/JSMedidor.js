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
    const btnGuardar = document.getElementById("btnGuardar");

    async function consultar(url, datos, descripcion) {
        const respuesta = await fetch(url, { method: "POST", body: datos });
        if (!respuesta.ok) {
            throw new Error(`Error HTTP ${respuesta.status} al ${descripcion}`);
        }
        return respuesta.json();
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
        btnGuardar.dataset.id = medidor.idmedidor || idcontrato;
        btnGuardar.disabled = false;
    } catch (error) {
        console.error(error);
        await Swal.fire("No se pudo cargar el medidor", error.message, "error");
    }
});
