function validarCampo(campo) {
    const vacio = campo.value.trim() === "";
    campo.classList.toggle("is-invalid", vacio);
    campo.classList.toggle("is-valid", !vacio);

    return !vacio;
}

const formulario = document.getElementById("formulario");
const btnGuardar = document.getElementById("btnGuardar");
const nombre = document.getElementById("nombre");

const campos = [nombre];

if (formulario) {
    ////////////////////VALIDACION DE CAMPOS VACIOS
    campos.forEach(function (campo) {
        campo.addEventListener("blur", function () {
            validarCampo(campo);
        });
    });

    ////////////////////PASAR TODO A MAYUSCULAS
    const campos1 = [nombre];

    campos1.forEach(function (campo) {
        campo.addEventListener("input", function () {
            campo.value = campo.value.toUpperCase();
            validarCampo(campo);
        });
    });

    btnGuardar.addEventListener("click", async function () {
        const nombreValido = validarCampo(nombre);

        if (btnGuardar.textContent.trim() == "Guardar") {
            if (nombreValido) {
                try {
                    const datos = new FormData(formulario);
                    datos.append("opcion", "guardar");

                    btnGuardar.disabled = true;
                    btnGuardar.textContent = "Procesando...";

                    const respuesta = await fetch("DAOCargo.php", {
                        method: "POST",
                        body: datos
                    });

                    if (!respuesta.ok) {
                        throw new Error("Error HTTP: " + respuesta.status);
                    }

                    const resultado = await respuesta.json();
                    if (resultado.estado) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: "Poseidón",
                            text: resultado.mensaje,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        limpiarCampos();
                    } else {
                        Swal.fire({
                            position: "top-end",
                            icon: "danger",
                            title: "Poseidón",
                            text: resultado.mensaje,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                } catch (error) {

                } finally {
                    btnGuardar.disabled = false;
                    btnGuardar.textContent = "Guardar";
                }
            }
        }

        if (btnGuardar.textContent.trim() == "Modificar") {
            if (nombreValido) {
                try {
                    const datos = new FormData(formulario);
                    datos.append("opcion", "modificar");
                    datos.append("id_cargo", btnGuardar.dataset.id);

                    btnGuardar.disabled = true;
                    btnGuardar.textContent = "Procesando...";

                    const respuesta = await fetch("DAOCargo.php", {
                        method: "POST",
                        body: datos
                    });

                    if (!respuesta.ok) {
                        throw new Error("Error HTTP: " + respuesta.status);
                    }

                    const resultado = await respuesta.json();
                    if (resultado.estado) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: "Poseidón",
                            text: resultado.mensaje,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = "Cargo.php";
                        });
                    } else {
                        Swal.fire({
                            position: "top-end",
                            icon: "danger",
                            title: "Poseidón",
                            text: resultado.mensaje,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                } catch (error) {

                } finally {
                    btnGuardar.disabled = false;
                    btnGuardar.textContent = "Guardar";
                }
            }
        }
    });
}

///////////////////LIMPIAR TODOS LOS CAMPOS///////////
function limpiarCampos() {
    campos.forEach(function (campo) {
        campo.value = "";
        campo.classList.remove("is-invalid");
        campo.classList.remove("is-valid");
    });
}

//////////PROCESA AL TERMINAR DE CARGAR LA PAGINA
document.addEventListener("DOMContentLoaded", async () => {
    const parametros = new URLSearchParams(window.location.search);
    const id_cargo = parametros.get("id");

    if (id_cargo) {
        try {
            const datos = new FormData();
            datos.append("opcion", "consultarIndividual");
            datos.append("id_cargo", id_cargo);

            Swal.fire({
                title: "Consultando...",
                text: "Espere",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });

            const respuesta = await fetch("DAOCargo.php", {
                method: "POST",
                body: datos
            });

            if (!respuesta.ok) {
                throw new Error("Error HTTP: " + respuesta.status);
            }

            const resultado = await respuesta.json();

            Swal.close();

            if (resultado.estado) {
                nombre.value = resultado.datos.nombre;

                btnGuardar.textContent = "Modificar";
                btnGuardar.dataset.id = resultado.datos.id_cargo;
            }
        } catch (error) {
            Swal.close();
        }
    }
});
