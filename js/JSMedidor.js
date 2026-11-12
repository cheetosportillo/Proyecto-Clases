// Cargar selects al iniciar
document.addEventListener("DOMContentLoaded", async () => {
    // 1. Cargar Cantones
    const resCanton = await fetch("DAOCanton.php", { method: "POST", body: new FormData().append("opcion", "consultar") });
    // 2. Cargar Rutas
    const resRuta = await fetch("DAORuta.php", { method: "POST", body: new FormData().append("opcion", "consultar") });

    // 3. Obtener id de URL: ?id=
    const params = new URLSearchParams(window.location.search);
    const idcontrato = params.get("id");

    if (idcontrato) {
        const datos = new FormData();
        datos.append("opcion", "consultarMedidor");
        datos.append("idcontrato", idcontrato);

        const resp = await fetch("DAOContrato.php", { method: "POST", body: datos });
        const res = await resp.json();

        if (res.estado) {
            // Llenar campos
            dui.value = res.datos.dui;
            nombre.value = res.datos.nombre + " " + res.datos.apellido;
            numero.value = res.datos.ncontrato;
            fecha.value = res.datos.fecha;
            canton.value = res.datos.idcanton;
            ruta.value = res.datos.idruta;
            direccion.value = res.datos.direccion || "";
            referencia.value = res.datos.referencia || "";
            nmedidor.value = res.datos.nmedidor || "";
            inicio.value = res.datos.inicio !== null ? res.datos.inicio : "";
            estado.value = res.datos.estado || "Activo";

            if (!res.datos.nmedidor || res.datos.nmedidor === "") {
                btnGuardar.textContent = "Guardar";
                btnGuardar.dataset.id = idcontrato;
                btnGuardar.disabled = false;
            } else {
                btnGuardar.textContent = "Modificar";
                btnGuardar.dataset.id = res.datos.idmedidor;
                btnGuardar.disabled = false;
            }
        }
    }
});