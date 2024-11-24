<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="./style/style.css">
</head>

<body>
    <nav class="navbar">
        <a href="index.php">Cotizaciones:</a>
        <a href="modificar.php">Modificar</a>
        <button id="contenidoArchivo" class="c"></button>
        <a class="image-button" href="javascript:history.back()">
            <img src="./images/atras.png" alt="">
        </a>
    </nav>
    <?php
    include "scripts/conexion.php";
    include "scripts/scripts.php";
    // Captura los parámetros pasados a través de la URL
    $searchBy = isset($_GET['searchBy']) ? $_GET['searchBy'] : '';
    $searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : '';

    // Si se envió el nombre del proyecto, cambiar la búsqueda a por número
    if ($searchBy === 'nombre') {
        // Realizar la búsqueda por número (nm_juridicas_numid) según el nombre del proyecto
        $sqlProyecto = $conexion->query("SELECT * FROM projects WHERE Name LIKE '%$searchQuery%'");
        $proyecto = $sqlProyecto->fetch_object();

        // Aquí obtendrás el 'nm_juridicas_numid' según el nombre
        $searchQuery = $proyecto->nm_juridicas_numid;
    }

    // Si el criterio de búsqueda es por número, buscar directamente por 'nm_juridicas_numid'
    if ($searchBy === 'numero') {
        // Realizar la búsqueda por número (nm_juridicas_numid)
        $sqlProyecto = $conexion->query("SELECT * FROM projects WHERE nm_juridicas_numid = '$searchQuery'");
        $proyecto = $sqlProyecto->fetch_object();
    }

    // Consulta para obtener los contactos del proyecto usando el 'nm_juridicas_numid'
    $sqlContacto = $conexion->query("SELECT * FROM nm_contactos WHERE id_contacto = '$searchQuery'");
    $contacto = $sqlContacto->fetch_object(); // Asumiendo un único contacto

    $fechaFormateada = date("d/m/Y H:i");
    ?>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $divisa = isset($_POST['id_moneda']) ? $_POST['id_moneda'] : null;
        $costcenter = isset($_POST['costcenter']) ? $_POST['costcenter'] : null;
        $cliente = isset($_POST['cliente']) ? $_POST['cliente'] : null;
        $ciudad = isset($_POST['ciudad']) ? $_POST['ciudad'] : null;
        $incoterm = isset($_POST['id_incoterm']) ? $_POST['id_incoterm'] : null;
        $pais = isset($_POST['id_pais']) ? $_POST['id_pais'] : null;
        $deliverytime = isset($_POST['id_deliverytime']) ? $_POST['id_deliverytime'] : null;
        $terminopago = isset($_POST['id_terminopago']) ? $_POST['id_terminopago'] : null;
        $offervalidity = isset($_POST['id_offervalidity']) ? $_POST['id_offervalidity'] : null;
        $fechacatual = isset($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');

        // Validación básica
        if (empty($name)) {
            die("Error: No se recibió el nombre del proyecto.");
        }
        if (empty($divisa) || empty($costcenter) || empty($cliente) || empty($ciudad) || empty($incoterm) || empty($pais) || empty($deliverytime) || empty($terminopago) || empty($offervalidity)) {
            die("Error: Faltan campos requeridos.");
        }

        // Obtener razon_social
        $query = $conexion->prepare("SELECT numid, razon_social FROM nm_juridicas WHERE numid = ?");
        $query->bind_param("i", $cliente);
        $query->execute();
        $result = $query->get_result();

        if ($row = $result->fetch_object()) {
            $razonSocial = $row->razon_social;
        } else {
            echo "<script>alert('Equipo no encontrado');</script>";
            exit;
        }

        // Consultar sector industrial
        $query = "SELECT razon_social, industrialsectors_id FROM nm_juridicas WHERE razon_social = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $razonSocial);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_object()) {
            $sectorIndustrial = $row->industrialsectors_id;
        } else {
            echo "<script>alert('Sector industrial no encontrado');</script>";
            exit;
        }

        // Obtener ip_dtbasicos_sec_basico
        $query = $conexion->prepare("SELECT ip_dtbasicos_sec_basico FROM industrialsectors WHERE id = ?");
        $query->bind_param("i", $sectorIndustrial);
        $query->execute();
        $result = $query->get_result();

        if ($row = $result->fetch_object()) {
            $ip_dtbasicos_sec_basico = $row->ip_dtbasicos_sec_basico;
        } else {
            echo "<script>alert('IP no encontrada');</script>";
            exit;
        }

        // Obtener id
        $query = $conexion->prepare("SELECT id FROM projects WHERE nm_juridicas_numid = ?");
        $query->bind_param("i", $searchQuery);
        $query->execute();
        $result = $query->get_result();

        if ($row = $result->fetch_object()) {
            $idproject = $row->id;
        } else {
            echo "<script>alert('Id no encontrada');</script>";
            exit;
        }

        // Actualizar datos en la tabla projects
        $query = "UPDATE projects 
    SET DateTime = ?, nm_juridicas_numid = ?, Name = ?, am_monedas_id = ?, CostCenter = ?, IndustrialSectors_Id = ?, np_ciudades_id_ciudad = ?, cp_incoterm_Id_incoterm = ?, np_paises_id_pais = ?, DeliveryTime_Id = ?, vp_terminospago_id_termino = ?, OfferValidity_Id = ?, ip_dtbasicos_sec_basico = ?
    WHERE id = ?";

        if ($stmt = $conexion->prepare($query)) {
            $stmt->bind_param(
                "sississssssssi",
                $fechacatual,
                $searchQuery,
                $name,
                $divisa,
                $costcenter,
                $sectorIndustrial,
                $ciudad,
                $incoterm,
                $pais,
                $deliverytime,
                $terminopago,
                $offervalidity,
                $ip_dtbasicos_sec_basico,
                $idproject
            );

            if ($stmt->execute()) {
                // Actualizar datos en ProjectsContacts
                $nm_contactos_id_contacto = $_POST['id_contacto'];
                $queryContacts = "UPDATE projectscontacts 
            SET nm_contactos_id_contacto = ?, ip_dtbasicos_sec_basico = ?
            WHERE projects_id = ?";

                if ($stmtContacts = $conexion->prepare($queryContacts)) {
                    $stmtContacts->bind_param(
                        "iii",
                        $nm_contactos_id_contacto,
                        $ip_dtbasicos_sec_basico,
                        $idproject// Usando el ID del proyecto
                    );

                    if (!$stmtContacts->execute()) {
                        echo "Error al actualizar el contacto del proyecto: " . $stmtContacts->error;
                    }
                    $stmtContacts->close();
                } else {
                    echo "Error al preparar la consulta de contactos: " . $conexion->error;
                }
                // Actualizar Cliente
                $queryContacts = "UPDATE nm_juridicas 
                SET razon_social = ?
                WHERE numid = ?";
                if ($stmtClient = $conexion->prepare($queryContacts)) {
                    $stmtClient->bind_param(
                        "si",
                        $razonSocial,
                        $searchQuery
                    );

                    if (!$stmtClient->execute()) {
                        echo "Error al actualizar el cliente en el proyecto: " . $stmtClient->error;
                    }
                    $stmtClient->close();
                } else {
                    echo "Error al preparar la consulta de actualización de clientes: " . $conexion->error;
                }


                // Actualizar exclusiones seleccionadas en Projects_Exclusions
                if (isset($_POST['exclusions']) && is_array($_POST['exclusions'])) {
                    $queryExclusions = "UPDATE Projects_Exclusions 
                SET exclusion_id = ?, ip_dtbasicos_sec_basico = ?
                WHERE projects_id = ?";

                    foreach ($_POST['exclusions'] as $exclusion_id) {
                        if ($stmtExclusions = $conexion->prepare($queryExclusions)) {
                            $stmtExclusions->bind_param(
                                "iii",
                                $exclusion_id,
                                $ip_dtbasicos_sec_basico,
                                $idproject // Usando el ID del proyecto
                            );

                            if (!$stmtExclusions->execute()) {
                                echo "Error al actualizar exclusión: " . $stmtExclusions->error;
                            }

                            $stmtExclusions->close();
                        } else {
                            echo "Error al preparar la consulta de exclusiones: " . $conexion->error;
                        }
                    }
                }

                // Redirigir a la página de modificación con el ID del proyecto actualizado
                echo '<script type="text/javascript">window.location.href = "modificar.php";</script>';
                exit;
            } else {
                echo "Error al actualizar el proyecto: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error al preparar la consulta: " . $conexion->error;
        }
    }
    ?>
    <div class="container-fluid mt-3 align-items-center">
        <h5>Información del Proyecto</h5>
        <form method="POST">
            <!-- Primera fila -->
            <div class="row mb-2">
                <!-- Número -->
                <div class="col-auto">
                    <label for="numero" class="form-label">Número:</label>
                </div>
                <div class="col-auto">
                    <label class="highlight" id="numero" name="numero"><?= htmlspecialchars($searchQuery) ?></label>
                </div>
                <!-- Nombre del Proyecto -->
                <div class="col-auto">
                    <label for="name" class="form-label">Nombre del Proyecto:</label>
                </div>
                <div class="col-auto">
                    <input type="text" class="form-control" id="name" name="name" value="<?= $proyecto ? htmlspecialchars($proyecto->Name) : '' ?>">
                </div>
                <!-- Divisa -->
                <div class="col-auto">
                    <label for="divisa" class="form-label">Divisa:</label>
                </div>
                <div class="col-auto">
                    <select class="form-select transparent-select" aria-label="Moneda" id="divisa" name="id_moneda" onchange="actualizarCosto()">
                        <option selected disabled>Selecciona una moneda</option>
                        <?php
                        $query = "SELECT id, pais, currency FROM am_monedas WHERE estado = 1";
                        $result = $conexion->query($query);
                        ?>
                        <?php while ($row = $result->fetch_object()) { ?>
                            <option value="<?= $row->id ?>"><?= htmlspecialchars($row->currency) ?> - <?= htmlspecialchars($row->pais) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <!-- Centro de Costo -->
                <div class="col-auto">
                    <label for="centroCosto" class="form-label">Centro de Costo:</label>
                </div>
                <div class="col-md-1">
                    <input type="text" class="form-control" id="costcenter" name="costcenter" value="<?= $proyecto ? htmlspecialchars($proyecto->CostCenter) : '' ?>">
                </div>
            </div>
            <!-- Segunda fila -->
            <div class="row mb-2">
                <!-- Fecha y Hora -->
                <div class="col-auto">
                    <label for="fechaHora" class="form-label">Fecha y Hora:</label>
                </div>
                <div class="col-auto">
                    <label class="highlight" id="fecha" name="fecha"><?= $fechaFormateada ?></label>
                </div>
                <div class="col-auto">
                    <label for="cliente" class="form-label">Cliente:</label>
                </div>
                <?php
                // Consulta para obtener los clientes
                $query = " SELECT numid, razon_social 
                            FROM nm_juridicas 
                            GROUP BY razon_social;";
                $result = $conexion->query($query);
                ?>

                <div class="col-auto">
                    <select class="form-select transparent-select" aria-label="cliente" id="clienteSelect" name="cliente" onchange="actualizarSectorIndustrial()">
                        <option selected disabled>Selecciona el cliente</option>
                        <?php while ($row = $result->fetch_object()) { ?>
                            <option value="<?= $row->numid ?>">
                                <?= htmlspecialchars($row->razon_social) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Sector Industrial -->
                <div class="col-auto">
                    <label for="sectorIndustrial" class="form-label">Sector Industrial:</label>
                </div>
                <div class="col-md-1 ps-1">
                    <label id="sectorIndustrialLabel" class="highlight" name="sectorindustrial">Selecciona un cliente</label>
                </div>
                <!-- Ciudad -->
                <div class="col-auto">
                    <label for="ciudad" class="form-label">Ciudad:</label>
                </div>
                <?php
                // Consulta para obtener las ciudades
                $query = "SELECT id_ciudad, nom_ciudad FROM np_ciudades";
                $result = $conexion->query($query);
                ?>
                <div class="col-auto">
                    <select class="form-select transparent-select" aria-label="ciudad" id="ciudadSelect" name="ciudad" onchange="actualizardepto()">
                        <option selected disabled>Selecciona la ciudad</option>
                        <?php while ($row = $result->fetch_object()) { ?>
                            <option value="<?= $row->id_ciudad ?>">
                                <?= htmlspecialchars($row->nom_ciudad) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <!-- Departamento -->
                <div class="col-auto">
                    <label for="departamento" class="form-label">Departamento:</label>
                </div>
                <div class="col-md-1 ps-1">
                    <label class="highlight" id="departamentoLabel" name="departamento">Seleccione una ciudad</label>
                </div>
            </div>
            <h5>Contactos para el proyecto</h5>
            <!-- Tercera fila -->
            <div class="row mb-2 align-items-center">
                <!-- Nombre -->
                <div class="col-auto">
                    <label for="nombre" class="form-label">Nombre:</label>
                </div>
                <div class="col-md-2 ps-1">
                    <select class="form-select transparent-select" aria-label="nombre" id="nombreSelect" name="id_contacto" onchange="actualizarContacto()">
                        <option selected disabled>Selecciona un contacto</option>
                        <?php
                        // Consulta para obtener los contactos
                        $query = "SELECT id_contacto, nom_contacto FROM nm_contactos";
                        $result = $conexion->query($query);
                        while ($row = $result->fetch_object()) {
                            echo "<option value='{$row->id_contacto}'>" . htmlspecialchars($row->nom_contacto) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Celular -->
                <div class="col-auto">
                    <label for="celular" class="form-label">Celular:</label>
                </div>
                <div class="col-md-1 ps-1">
                    <label class="highlight" id="celularLabel">Seleccione un contacto</label>
                </div>

                <!-- Correo -->
                <div class="col-auto">
                    <label for="correo" class="form-label">Correo:</label>
                </div>
                <div class="col-md-1 ps-1">
                    <label class="highlight" id="correoLabel">Seleccione un contacto</label>
                </div>
            </div>
            <button type="button" class="btn btn-primary custom-border" onclick="window.location.href='servicio.php?id=<?= $searchQuery ?>'">
                <i class="bi bi-list"></i>
            </button>
            <h5>Bienes y Servicios para el proyecto</h5>
            <div class="row mb-2 justify-content-end">
                <div class="col-auto">
                    <label for="costo" class="form-label">Costo:</label>
                </div>
                <div class="col-auto">
                    <label class="highlight" id="totalPriceLabel">
                        Esperando selección de divisa...
                    </label>
                </div>
            </div>
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Servicio elegido</th>
                        <th scope="col">Detalle del servicio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include "scripts/conexion.php";
                    $sql = $conexion->query("SELECT 
              s.description AS 'Servicioelegido',
              CONCAT(c.description, ' ', c.quiantity) AS 'Detalledelservicio'
          FROM 
              Projects_Services ps
          JOIN 
              Services s ON ps.services_id = s.id
          JOIN 
              Complements c ON s.complements_id = c.id
          WHERE 
              ps.projects_id = $searchQuery
          GROUP BY 
              s.description, CONCAT(c.description, ' ', c.quiantity);
          ");
                    while ($datos = $sql->fetch_object()) { ?>
                        <tr>
                            <td><?= $datos->Servicioelegido ?></td>
                            <td><?= $datos->Detalledelservicio ?></td>
                        </tr>
                    <?php }
                    ?>
                </tbody>
            </table>
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Equipo elegido</th>
                        <th scope="col">Marca</th>
                        <th scope="col">Modelo</th>
                        <th scope="col">Referencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = $conexion->query("SELECT 
              t.description AS 'descripcion',
              e.trademarc AS 'marca',
              e.model AS 'modelo',
              e.refernce AS 'referencia'
          FROM 
              Projects_Equipments pe
          JOIN 
              Equipments e ON pe.equipments_id = e.id
          JOIN 
              Equipmentstypes t ON e.equipmentstypes_id = t.id
          WHERE 
              pe.projects_id = $searchQuery");
                    while ($datos = $sql->fetch_object()) { ?>
                        <tr>
                            <td><?= $datos->descripcion ?></td>
                            <td><?= $datos->marca ?></td>
                            <td><?= $datos->modelo ?></td>
                            <td><?= $datos->referencia ?></td>
                        </tr>
                    <?php }
                    ?>
                </tbody>
            </table>
            <h5>condiciones del proyecto</h5>
            <div class="row mb-1">
                <div class="col-auto">
                    <label for="exclusiones" class="form-label">Exclusiones:</label>
                </div>
                <div class="col-auto">
                    <?php
                    // Consulta para obtener las exclusiones
                    $query = "SELECT id, description FROM exclusions";
                    $result = $conexion->query($query);

                    while ($row = $result->fetch_object()) {
                        echo '<div class="form-check d-flex justify-content-between">';
                        echo '<label class="form-check-label flex-grow-1 exclusion-label" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="' . htmlspecialchars($row->description) . '">';
                        echo htmlspecialchars($row->description);
                        echo '</label>';
                        echo '<input class="form-check-input ms-2" type="checkbox" name="exclusions[]" value="' . $row->id . '" id="exclusion' . $row->id . '">';
                        echo '</div>';
                    }
                    ?>
                </div>

                <div class="col-auto">
                    <div class="row mb-2">
                        <div class="form-group">
                            <label for="tipo-entrega" class="form-label col-md-3">Tipo Entrega:</label>
                            <?php
                            // Consulta para obtener cp_incoterm
                            $query = "SELECT id_incoterm, descrip FROM cp_incoterm;";
                            $result = $conexion->query($query);
                            ?>
                            <select class="form-select transparent-select" aria-label="incoterm" id="incotermSelect" name="id_incoterm">
                                <option selected disabled>Selecciona el tipo de entrega</option>
                                <?php while ($row = $result->fetch_object()) { ?>
                                    <option value="<?= $row->id_incoterm ?>">
                                        <?= htmlspecialchars($row->descrip) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pais-entrega" class="form-label col-md-3">País Entrega:</label>
                            <?php
                            // Consulta para obtener paises
                            $query = "SELECT id_pais, nom_pais FROM np_paises";
                            $result = $conexion->query($query);
                            ?>
                            <select class="form-select transparent-select" aria-label="pais" id="paisSelect" name="id_pais">
                                <option selected disabled>Selecciona el pais</option>
                                <?php while ($row = $result->fetch_object()) { ?>
                                    <option value="<?= $row->id_pais ?>">
                                        <?= htmlspecialchars($row->nom_pais) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="row mb-2">
                        <div class="form-group">
                            <label for="tiempo-entrega" class="form-label col-md-5">Tiempo Entrega:</label>
                            <?php
                            // Consulta para obtener DeliveryTime
                            $query = "SELECT id, CONCAT(time, ' ', note) AS time_and_note 
            FROM deliverytime;";
                            $result = $conexion->query($query);
                            ?>
                            <select class="form-select transparent-select" aria-label="deliverytime" id="deliverySelect" name="id_deliverytime">
                                <option selected disabled>Selecciona el tiempo de entrega</option>
                                <?php while ($row = $result->fetch_object()) { ?>
                                    <option value="<?= $row->id ?>">
                                        <?= htmlspecialchars($row->time_and_note) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="modo-pago" class="form-label col-md-5">Modo de Pago:</label>
                            <?php
                            // Consulta para obtener el modo de pago
                            $query = "SELECT id_termino, descrip FROM vp_terminospago;";
                            $result = $conexion->query($query);
                            ?>
                            <select class="form-select transparent-select" aria-label="terminopago" id="terminoSelect" name="id_terminopago">
                                <option selected disabled>Selecciona el modo de pago</option>
                                <?php while ($row = $result->fetch_object()) { ?>
                                    <option value="<?= $row->id_termino ?>">
                                        <?= htmlspecialchars($row->descrip) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="validez-oferta" class="form-label col-md-5">Validez de la oferta:</label>
                            <?php
                            // Consulta para obtener offervalidity
                            $query = "SELECT id, CONCAT(days, ' ', note) AS time_and_note 
            FROM offervalidity;";
                            $result = $conexion->query($query);
                            ?>
                            <select class="form-select transparent-select" aria-label="offervalidity" id="offertSelect" name="id_offervalidity">
                                <option selected disabled>Selecciona la validez de la oferta</option>
                                <?php while ($row = $result->fetch_object()) { ?>
                                    <option value="<?= $row->id ?>">
                                        <?= htmlspecialchars($row->time_and_note) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <br>
                </div>
                <div class="col-auto">
                    <button type="submit" class="image-button2">
                        <img src="/images/Picture1.png" alt="Botón de Imagen" class="button-image">
                    </button>
                </div>
        </form>
    </div>
    <div class="cubo-soft">
        <span>.... . . . . CUBO Soft</span>
    </div>
    <div class="linea-izquierda"></div>
    <div class="linea-abajo"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>