function validarCampo(campo) {
    const vacio = campo.value.trim() === "";
    campo.classList.toggle("is-invalid", vacio);
    campo.classList.toggle("is-valid", !vacio);

    return !vacio;
}

const formulario = document.getElementById("formulario");
const btnGuardar = document.getElementById("btnGuardar");
const dui = document.getElementById("dui");
const nombre = document.getElementById("nombre");
const apellido = document.getElementById("apellido");
const telefono = document.getElementById("telefono");

const campos = [dui, nombre, apellido, telefono];

if (formulario) {
    ////////////////////VALIDACION DE CAMPOS VACIOS


    campos.forEach(function (campo) {
        campo.addEventListener("blur", function () {
            validarCampo(campo);
        });
    });
    ////////////////////PASAR TODO A MAYUSCULAS
    const campos1 = [nombre, apellido];

    campos1.forEach(function (campo) {
        campo.addEventListener("input", function () {
            campo.value = campo.value.toUpperCase();
            validarCampo(campo);
        });
    });

    //////////////////////////FORMATO DE DUI//////////////

    dui.addEventListener("input", function () {
        let valor = dui.value.replace(/\D/g, "");
        valor = valor.substring(0, 9);
        if (valor.length > 8) {
            valor = valor.substring(0, 8) + "-" + valor.substring(8);
        }
        dui.value = valor;
    });

    telefono.addEventListener("input", function () {
        let valor = telefono.value.replace(/\D/g, "");
        valor = valor.substring(0, 8);
        if (valor.length > 5) {
            valor = valor.substring(0, 4) + "-" + valor.substring(4);
        }
        telefono.value = valor;
    });

    ////////////////////BUSQUEDA DE DUI- PARA REPERIDOS/////////

    dui.addEventListener("keyup", async function () {
        if (dui.value.length === 10) {
            try {
                const datos = new FormData();
                datos.append("opcion", "consultarDUI");
                datos.append("dui", dui.value);

                Swal.fire({
                    title: "Espere...",
                    text: "Consultando DUI",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading()
                });

                const respuesta = await fetch("DAOEmpleado.php", {
                    method: "POST",
                    body: datos
                });

                if (!respuesta.ok) {
                    console.error(respuesta.status);
                    throw new Error("Error HTTP: " + respuesta.status);
                }

                const resultado = await respuesta.json();

                // Cerrar el mensaje de espera
                Swal.close();

                if (resultado.estado) {
                    Swal.fire({
                        position: "top-end",
                        icon: "warning",
                        title: "Poseidón",
                        text: resultado.mensaje,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    dui.classList.toggle("is-invalid", true);
                }

            } catch (error) {
                Swal.close();
            }
        }
    });



    btnGuardar.addEventListener("click", async function () {
        const duiValido = validarCampo(dui);
        const nombreValido = validarCampo(nombre);
        const apellidoValido = validarCampo(apellido);
        const telefonoValido = validarCampo(telefono);

        if (btnGuardar.textContent.trim() == "Guardar") {

            if (duiValido && nombreValido && apellidoValido && telefonoValido) {
                try {
                    const datos = new FormData(formulario);
                    datos.append("opcion", "guardar");

                    btnGuardar.disabled = true;
                    btnGuardar.textContent = "Procesando...";

                    // Enviar datos 
                    // await hace que JavaScript ESPERE
                    const respuesta = await fetch("DAOEmpleado.php", {
                        method: "POST",
                        body: datos
                    });

                    // Verificar respuesta HTTP
                    if (!respuesta.ok) {
                        throw new Error("Error HTTP: " + respuesta.status);
                    }

                    // Esperar a que DAO termine
                    const resultado = await respuesta.json();
                    if (resultado.estado) {
                        //todo salio bien
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
                    btnGuardar, this.textContent = "Guardar";
                }

            }
        }
        if (btnGuardar.textContent.trim() == "Modificar") {

            if (duiValido && nombreValido && apellidoValido && telefonoValido) {
                try {
                    const datos = new FormData(formulario);
                    datos.append("opcion", "modificar");
                    datos.append("idempleado", btnGuardar.dataset.id);

                    btnGuardar.disabled = true;
                    btnGuardar.textContent = "Procesando...";

                    // Enviar datos 
                    // await hace que JavaScript ESPERE
                    const respuesta = await fetch("DAOEmpleado.php", {
                        method: "POST",
                        body: datos
                    });

                    // Verificar respuesta HTTP
                    if (!respuesta.ok) {
                        throw new Error("Error HTTP: " + respuesta.status);
                    }

                    // Esperar a que DAO termine
                    const resultado = await respuesta.json();
                    if (resultado.estado) {
                        //todo salio bien
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: "Poseidón",
                            text: resultado.mensaje,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {//cuando desaparece el mensaje se recarga la pagina
                            window.location.href = "empleado.php";
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
                    btnGuardar, this.textContent = "Guardar";
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
    // Obtener el ID de la URL
    const parametros = new URLSearchParams(window.location.search);
    const idempleado = parametros.get("id");

    if (idempleado) {//SI EXISTE EL ID, SE HACE LA BUSQUEDA
        try {
            const datos = new FormData();
            datos.append("opcion", "consultarIndividual");
            datos.append("idempleado", idempleado);

            Swal.fire({
                title: "Consultando...",
                text: "Espere",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });

            const respuesta = await fetch("DAOEmpleado.php", {
                method: "POST",
                body: datos
            });

            if (!respuesta.ok) {
                console.error(respuesta.status);
                throw new Error("Error HTTP: " + respuesta.status);
            }

            const resultado = await respuesta.json();


            // Cerrar el mensaje de espera
            Swal.close();

            if (resultado.estado) {
                dui.value = resultado.datos.dui;
                nombre.value = resultado.datos.nombre;
                apellido.value = resultado.datos.apellido;
                telefono.value = resultado.datos.telefono;

                btnGuardar.textContent = "Modificar";
                btnGuardar.dataset.id = resultado.datos.idempleado;
            }

        } catch (error) {
            Swal.close();
        }
    }


});

