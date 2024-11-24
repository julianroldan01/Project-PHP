<script>
    // Función para cargar el contenido de Cliente.txt automáticamente
    function cargarArchivo() {
        fetch('Cliente.txt')
            .then(response => {
                if (!response.ok) {
                    throw new Error('No se pudo leer el archivo');
                }
                return response.text();
            })
            .then(texto => {
                document.getElementById('contenidoArchivo').textContent = texto;
            })
            .catch(error => {
                document.getElementById('contenidoArchivo').textContent = 'Error: ' + error.message;
            });
    }

    // Llamar a la función cuando se carga la página
    window.onload = cargarArchivo;
</script>

<script>
    function resetForm() {
        // Limpiar todos los inputs del formulario
        const inputs = document.querySelectorAll('form input');
        inputs.forEach(input => {
            input.value = ''; // Establecer el valor vacío
        });
    }
</script>

<script>
    function actualizarSectorIndustrial() {
        const clienteSelect = document.getElementById('clienteSelect');
        const clienteId = clienteSelect.value;
        const sectorIndustrialLabel = document.getElementById('sectorIndustrialLabel');

        // Realiza una solicitud AJAX para obtener el sector industrial
        fetch(`get_sector_industrial.php?clienteId=${clienteId}`)
            .then(response => response.text())
            .then(data => {
                sectorIndustrialLabel.textContent = data;
            })
            .catch(error => {
                console.error('Error al obtener el sector industrial:', error);
                sectorIndustrialLabel.textContent = 'Error al cargar';
            });
    }
</script>

<script>
    function actualizardepto() {
        const ciudadSelect = document.getElementById('ciudadSelect');
        const ciudadId = ciudadSelect.value;

        const departamentoLabel = document.getElementById('departamentoLabel');

        // Realiza una solicitud AJAX para obtener el sector industrial
        fetch(`get_departamento.php?ciudadId=${ciudadId}`)
            .then(response => response.text())
            .then(data => {
                departamentoLabel.textContent = data;
            })
            .catch(error => {
                console.error('Error al obtener el departamento:', error);
                departamentoLabel.textContent = 'Error al cargar';
            });
    }
</script>
<script>
    function actualizarContacto() {
        const contactoId = document.getElementById('nombreSelect').value;

        // Realizar solicitud AJAX para obtener los datos del contacto
        fetch(`get_contacto.php?id_contacto=${contactoId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('celularLabel').textContent = data.tel_contacto || "No disponible";
                document.getElementById('correoLabel').textContent = data.email || "No disponible";
            })
            .catch(error => {
                console.error('Error al obtener los datos del contacto:', error);
                document.getElementById('celularLabel').textContent = "Error al cargar";
                document.getElementById('correoLabel').textContent = "Error al cargar";
            });
    }
</script>
<script>
    function cargarDetalleTecnicoYPrecio() {
        const marcaId = document.getElementById("marcaSelect").value;

        if (marcaId) {
            // Cargar detalles técnicos
            fetch(`obtenerDetalleTecnico.php?id=${marcaId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("detalleTecnico").innerHTML = data;
                })
                .catch(error => console.error('Error al cargar los detalles técnicos:', error));

            // Cargar precio
            fetch(`obtenerPrecio.php?marcaId=${marcaId}`)
                .then(response => response.json())
                .then(data => {
                    const precioLabel = document.getElementById('precioLabel');
                    if (data.error) {
                        precioLabel.textContent = data.error;
                    } else {
                        precioLabel.textContent = `${data.price} ${data.currency}`;
                    }
                })
                .catch(error => console.error('Error al obtener el precio:', error));
        }
    }
</script>
<script>
function actualizarCosto() {
    const divisaElement = document.getElementById("divisa");
    if (!divisaElement) {
        console.error("Elemento con ID 'divisa' no encontrado.");
        return;
    }

    const divisaId = divisaElement.value; // Obtener el id de la divisa seleccionada
    const projectEquipmentsId = document.getElementById("numero").textContent; // Obtener el valor de projects_equipments_id

    if (divisaId && projectEquipmentsId) {
        fetch(`obtenerPrecioPorDivisa.php?divisaId=${divisaId}&projectEquipmentsId=${projectEquipmentsId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const precioTotal = data.precioTotal;
                    const divisa = data.divisa;
                    // Actualizar el label con el precio y la divisa
                    document.getElementById("totalPriceLabel").textContent = `${precioTotal} ${divisa}`;
                } else {
                    alert("Error al obtener el precio.");
                }
            })
            .catch(error => console.error('Error al obtener el precio:', error));
    }
}
</script>

