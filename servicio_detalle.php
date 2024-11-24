<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./style/style.css">
</head>

<body>
    <nav class="navbar">
        <a href="index.php">Servicios:</a>
        <label class="label-nav" id="selectedServiceLabel">
            <?php
            include "scripts/scripts.php";
            // Verificar si el parámetro 'id' está presente en la URL
            if (isset($_GET['id'])) {
                include "scripts/conexion.php";
                $id = $_GET['id'];
                $id_project = $_GET['id_project'];
                // Consulta para obtener la descripción del servicio
                $sql = $conexion->query("SELECT description FROM services WHERE id = $id");

                // Verificar si se encontró la descripción
                if ($sql && $sql->num_rows > 0) {
                    $service = $sql->fetch_object();
                    echo htmlspecialchars($service->description); // Mostrar la descripción en el label
                } else {
                    echo "Servicio no encontrado";
                }
            } else {
                echo "Seleccione un servicio";
            }
            ?>
        </label>
        <button id="contenidoArchivo" class="c"></button>
        <a class="image-button" href="javascript:history.back()">
            <img src="./images/atras.png" alt="">
        </a>
    </nav>
    <div class="container-fluid">
        <br>
        <br>
        <br>
        <br>
        <label class="label-nav">
            <h4>Detalles del servicio</h4>
        </label>
        <br>
        <?php
        include "scripts/conexion.php";

        // Verificar si el parámetro 'id' está presente en la URL
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            // Consulta para obtener los detalles del servicio y su complemento
            $sql = $conexion->query("
        SELECT 
            s.id AS 'services_id', 
            s.complements_id, 
            s.description AS 'Servicioelegido', 
            CONCAT(c.description, ' ', c.quiantity) AS 'Detalledelservicio', 
            s.ip_dtbasicos_sec_basico
        FROM 
            Services s
        JOIN 
            Complements c ON s.complements_id = c.id
        WHERE 
            s.id = $id
    ");

            // Verificar si la consulta fue exitosa
            if ($sql) {
                // Verificar si se encontró el servicio y el complemento
                if ($sql->num_rows > 0) {
                    $datos = $sql->fetch_object();

                    // Asegurarse de que las propiedades del objeto existan
                    $servicio_elegido = isset($datos->Servicioelegido) ? $datos->Servicioelegido : '';
                    $detalles_servicio = isset($datos->Detalledelservicio) ? $datos->Detalledelservicio : '';
                    $complements_id = isset($datos->complements_id) ? $datos->complements_id : '';
                    $ip = isset($datos->ip_dtbasicos_sec_basico) ? $datos->ip_dtbasicos_sec_basico : '';
                    // Procesar la inserción al hacer clic en el botón
                    if (isset($_POST['insertar_servicio'])) {
                        // Preparar la consulta de inserción
                        $insertar = $conexion->prepare("INSERT INTO Projects_Services (projects_id, services_id, ip_dtbasicos_sec_basico)
    VALUES (?, ?, ?)");

                        // Verificar si la preparación de la consulta falla
                        if ($insertar === false) {
                            echo "Error al preparar la consulta: " . $conexion->error;
                        } else {
                            // Vincular los parámetros y ejecutar la consulta
                            $insertar->bind_param("sss", $id_project, $id, $ip);
                            $insertar->execute();

                            // Redirigir a crear.php directamente utilizando JavaScript
                            echo '<script type="text/javascript">
                            alert("Registro agregado exitosamente.");
                            window.location.href = "http://localhost:3000/servicio_detalle.php?id='. ($id) . '&id_project='. ($id_project) . '";
                        </script>';
                        
                        
                            exit();
                        }
                    }


                    // Mostrar los detalles del servicio y el complemento
                    echo '<div class="row mb-4 d-flex justify-content-center align-items-center">';
                    echo '<div class="col-lg-11 col-md-7 d-flex justify-content-center">';
                    echo '<table class="table w-100">';
                    echo '<thead class="table-light">
                    <tr>
                        <th scope="col">Servicio elegido</th>
                        <th scope="col">Detalle del servicio</th>
                    </tr>
                  </thead>';
                    echo '<tbody>';

                    // Mostrar el servicio y su detalle
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($servicio_elegido) . '</td>';
                    echo '<td>' . htmlspecialchars($detalles_servicio) . '</td>';
                    echo '</tr>';

                    echo '</tbody></table>';
                    echo '</div>';

                    echo '<div class="col-lg-1 col-md-1 d-flex justify-content-start align-items-start">';
                    echo '<form method="POST">
                    <button type="submit" name="insertar_servicio" class="image-button2">
                    <img src="/images/Picture2.png" alt="Botón de Imagen" class="button-image2">
                    </button>
                </form>';
                    echo '</div>';

                    echo '</div>';
                } else {
                    echo "<p>No se encontró el servicio.</p>";
                }
            } else {
                // Mostrar un mensaje de error si la consulta falló
                echo "<p>Error en la consulta SQL: " . $conexion->error . "</p>";
            }
        } else {
            echo "<p>Identificador de servicio no especificado.</p>";
        }
        ?>
    </div>
    <div class="cubo-soft">
        <span>.... . . . . CUBO Soft</span>
    </div>
    <div class="linea-izquierda"></div>
    <div class="linea-abajo"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>