document.addEventListener("DOMContentLoaded", () => {
    const inputNMedidor = document.getElementById("nmedidor");
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

        Swal.fire({ title: "Buscando...", didOpen: () => Swal.showLoading() });
        const res = await fetch("DAOLectura.php", {
            method: "POST",
            body: new URLSearchParams({ opcion: "consultarConsumo", nmedidor })
        });
        const json = await res.json();
        Swal.close();

        if (json.estado) {
            inputIdMedidor.value = json.datos.idmedidor;
            inputSocio.value = json.datos.socio;
            inputCanton.value = json.datos.canton;
            inputRuta.value = json.datos.ruta;
            inputDireccion.value = json.datos.direccion;
            inputLecturaAnterior.value = json.datos.lectura_anterior;
            inputFechaAnterior.value = json.datos.fecha_anterior || "";
            inputLecturaActual.value = "";
            inputLecturaActual.focus();
        } else {
            Swal.fire("Error", json.mensaje || "Medidor no encontrado", "error");
        }
    }

    inputNMedidor.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            buscarMedidor();
        }
    });

    // Guardar Lectura
    btnGuardar.addEventListener("click", async () => {
        const actual = parseInt(inputLecturaActual.value, 10);
        const anterior = parseInt(inputLecturaAnterior.value || "0", 10);

        if (isNaN(actual)) {
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