document.addEventListener("DOMContentLoaded", () => {
    const inputNMedidor = document.getElementById("nmedidor");
    const btnBuscarMedidor = document.getElementById("btnBuscarMedidor");
    const inputIdMedidor = document.getElementById("idmedidor");
    const inputSocio = document.getElementById("socio");
    const inputCanton = document.getElementById("canton");
    const inputRuta = document.getElementById("ruta");
    const inputDireccion = document.getElementById("direccion");
    const inputLecturaActual = document.getElementById("lectura_actual");
    const inputLecturaAnterior = document.getElementById("lectura_anterior");
    const inputFechaLectura = document.getElementById("fecha_lectura");
    const inputFechaAnterior = document.getElementById("fecha_anterior");
    const selectEmpleado = document.getElementById("idempleado");
    const inputAnio = document.getElementById("anio");
    const selectMes = document.getElementById("mes");
    const inputFechaVencimiento = document.getElementById("fecha_vencimiento");
    const inputAviso = document.getElementById("aviso");
    const btnGuardar = document.getElementById("btnGuardar");

    // Inicializar fechas
    const hoy = new Date();
    const yyyy = hoy.getFullYear();
    const mm = String(hoy.getMonth() + 1).padStart(2, "0");
    const dd = String(hoy.getDate()).padStart(2, "0");
    const hoyStr = `${yyyy}-${mm}-${dd}`;

    inputFechaLectura.value = hoyStr;
    inputFechaLectura.max = hoyStr; // Prevenir fechas futuras
    inputAnio.value = yyyy;
    selectMes.value = hoy.getMonth() + 1;
    inputFechaVencimiento.value = `${yyyy}-${mm}-25`;

    // Cargar empleados
    fetch("DAOEmpleado.php", {
        method: "POST",
        body: new URLSearchParams({ opcion: "consultar" })
    })
    .then(r => r.json())
    .then(data => {
        selectEmpleado.innerHTML = '<option value="">[SELECCIONE EMPLEADO]</option>';
        data.forEach(e => {
            selectEmpleado.innerHTML += `<option value="${e.idempleado}">${e.nombre} ${e.apellido}</option>`;
        });
    });

    // Buscar medidor por Enter o escáner
    async function buscarMedidor() {
        const nmedidor = inputNMedidor.value.trim();
        if (!nmedidor) return;

        inputIdMedidor.value = "";
        inputSocio.value = "";
        inputCanton.value = "";
        inputRuta.value = "";
        inputDireccion.value = "";
        inputLecturaActual.value = "";
        inputLecturaAnterior.value = "";
        inputFechaAnterior.value = "";

        Swal.fire({ title: "Buscando...", didOpen: () => Swal.showLoading() });
        let datosMedidor;
        let errorBusqueda;

        try {
            const respuesta = await fetch("DAOLectura.php", {
                method: "POST",
                body: new URLSearchParams({ opcion: "consultarConsumo", nmedidor })
            });
            const resultado = await respuesta.json();

            if (!respuesta.ok || !resultado.estado) {
                throw new Error(resultado.mensaje || `Error HTTP ${respuesta.status}`);
            }
            datosMedidor = resultado.datos;
        } catch (error) {
            console.error(error);
            errorBusqueda = error;
        } finally {
            Swal.close();
        }

        if (errorBusqueda) {
            await Swal.fire("No se pudo buscar el medidor", errorBusqueda.message, "error");
            inputNMedidor.focus();
            return;
        }

        inputIdMedidor.value = datosMedidor.idmedidor;
        inputSocio.value = datosMedidor.socio;
        inputCanton.value = datosMedidor.canton;
        inputRuta.value = datosMedidor.ruta;
        inputDireccion.value = datosMedidor.direccion;
        inputLecturaAnterior.value = datosMedidor.lectura_anterior;
        inputFechaAnterior.value = datosMedidor.fecha_anterior || "";
        inputLecturaActual.focus();
    }

    inputNMedidor.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            void buscarMedidor();
        }
    });
    btnBuscarMedidor.addEventListener("click", () => void buscarMedidor());

    // Guardar Lectura
    btnGuardar.addEventListener("click", async () => {
        const actual = parseFloat(inputLecturaActual.value);
        const anterior = parseFloat(inputLecturaAnterior.value || "0");

        if (!Number.isFinite(actual)) {
            Swal.fire("Atención", "Ingrese la lectura actual", "warning");
            return;
        }

        if (actual < anterior) {
            Swal.fire("Lectura Incorrecta", `La lectura actual (${actual}) no puede ser menor a la anterior (${anterior})`, "error");
            return;
        }

        const datos = new FormData();
        datos.append("opcion", "guardar");
        datos.append("idmedidor", inputIdMedidor.value);
        datos.append("idempleado", selectEmpleado.value);
        datos.append("fecha_lectura", inputFechaLectura.value);
        datos.append("fecha_anterior", inputFechaAnterior.value);
        datos.append("lectura_anterior", anterior);
        datos.append("lectura_actual", actual);
        datos.append("fecha_vencimiento", inputFechaVencimiento.value);
        datos.append("aviso", inputAviso.value);
        datos.append("mes", selectMes.value);
        datos.append("anio", inputAnio.value);

        const r = await fetch("DAOLectura.php", { method: "POST", body: datos });
        const res = await r.json();

        if (res.estado) {
            Swal.fire("Éxito", res.mensaje, "success").then(() => location.reload());
        } else {
            Swal.fire("Error", res.mensaje, "warning");
        }
    });
});