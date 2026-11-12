function validarCampo(campo) {
    const vacio = campo.value.trim() === "";
    campo.classList.toggle("is-invalid", vacio);
    campo.classList.toggle("is-valid", !vacio);
    return !vacio;
}

const formulario = document.getElementById("formulario");
const btnGuardar = document.getElementById("btnGuardar");
const btnCancelar = document.getElementById("btnCancelar");
const inputDesde = document.getElementById("desde");
const inputHasta = document.getElementById("hasta");
const selectTipo = document.getElementById("tipo");
const inputPrecio = document.getElementById("precio");
const campos = [inputDesde, inputHasta, selectTipo, inputPrecio];

if (formulario) {
    campos.forEach(campo => {
        campo.addEventListener("blur", () => validarCampo(campo));
        campo.addEventListener("input", () => validarCampo(campo));
    });

    btnGuardar?.addEventListener("click", async function () {
        let formValido = true;
        campos.forEach(campo => { if (!validarCampo(campo)) formValido = false; });
        if (!formValido) {
            Swal.fire("Atención", "Por favor complete todos los campos obligatorios (*)", "warning");
            return;
        }

        if (parseFloat(inputDesde.value) >= parseFloat(inputHasta.value)) {
            Swal.fire("Rango Incorrecto", "El valor 'Desde' debe ser menor que 'Hasta'", "error");
            return;
        }

        const esModificar = btnGuardar.textContent.trim() === "Modificar";
        const accion = esModificar ? "modificar" : "guardar";

        const datos = new FormData(formulario);
        datos.append("opcion", accion);
        if (esModificar) datos.append("id_tarifa", btnGuardar.dataset.id);

        btnGuardar.disabled = true;
        btnGuardar.textContent = "Procesando...";

        const resp = await fetch("DAOTarifa.php", { method: "POST", body: datos });
        const res = await resp.json();

        if (res.estado) {
            Swal.fire("Poseidón", res.mensaje, "success").then(() => {
                window.location.href = "lista-tarifa.php";
            });
        } else {
            Swal.fire("Error", res.mensaje, "error");
            btnGuardar.disabled = false;
            btnGuardar.textContent = esModificar ? "Modificar" : "Guardar";
        }
    });

    btnCancelar?.addEventListener("click", () => {
        window.location.href = "lista-tarifa.php";
    });
}

document.addEventListener("DOMContentLoaded", async () => {
    const id_tarifa = new URLSearchParams(window.location.search).get("id");
    if (id_tarifa) {
        const datos = new FormData();
        datos.append("opcion", "consultarIndividual");
        datos.append("id_tarifa", id_tarifa);

        const r = await fetch("DAOTarifa.php", { method: "POST", body: datos });
        const res = await r.json();
        if (res.estado) {
            inputDesde.value = res.datos.desde;
            inputHasta.value = res.datos.hasta;
            selectTipo.value = res.datos.tipo;
            inputPrecio.value = res.datos.precio;
            btnGuardar.textContent = "Modificar";
            btnGuardar.dataset.id = res.datos.id_tarifa;
        }
    }
});
