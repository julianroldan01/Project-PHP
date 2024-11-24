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
        <a href="visualizar.php">Lista</a>
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
        $fechaProyecto = $proyecto->DateTime;
        $id = $proyecto->Id;

        // Aquí obtendrás el 'nm_juridicas_numid' según el nombre
        $searchQuery = $proyecto->nm_juridicas_numid;
    }

    // Si el criterio de búsqueda es por número, buscar directamente por 'nm_juridicas_numid'
    if ($searchBy === 'numero') {
        // Realizar la búsqueda por número (nm_juridicas_numid)
        $sqlProyecto = $conexion->query("SELECT * FROM projects WHERE nm_juridicas_numid = '$searchQuery'");
        $proyecto = $sqlProyecto->fetch_object();
        $fechaProyecto = $proyecto->DateTime;
        $id = $proyecto->Id;
    }

    // Consulta para obtener los contactos del proyecto usando el 'id' de projects
    $sqlContacto = $conexion->query("SELECT * FROM projectscontacts WHERE projects_id = '$id'");
    $contacto = $sqlContacto->fetch_object();
    $id_contacto = $contacto->nm_contactos_id_contacto;

    // Consulta para obtener el currency desde la tabla am_monedas usando el id_moneda de la tabla projects
    $query = $conexion->prepare("SELECT currency FROM am_monedas WHERE id = ?");
    $query->bind_param("i", $proyecto->am_monedas_id);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_object()) {
        $currency = $row->currency;
    } else {
        echo "<script>alert('moneda no encontrada');</script>";
        exit;
    }

    //consulta para conseguir cliente
    $query = $conexion->prepare("SELECT razon_social,industrialsectors_id FROM nm_juridicas WHERE numid = ?");
    $query->bind_param("i", $proyecto->nm_juridicas_numid);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_object()) {
        $razonSocial = $row->razon_social;
        $industrialsectors_id = $row->industrialsectors_id;
    } else {
        echo "<script>alert('razon social no encontrado');</script>";
        exit;
    }

    //consulta para conseguir sector industrial
    $query = $conexion->prepare("SELECT Description FROM industrialsectors WHERE id = ?");
    $query->bind_param("i", $industrialsectors_id);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_object()) {
        $sectorIndustrial = $row->Description;
    } else {
        echo "<script>alert('sector industrial no encontrado');</script>";
        exit;
    }

    //consulta para conseguir ciudad
    $query = $conexion->prepare("SELECT nom_ciudad,id_dpto FROM np_ciudades WHERE id_ciudad = ?");
    $query->bind_param("i", $proyecto->np_ciudades_id_ciudad);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_object()) {
        $ciudad = $row->nom_ciudad;
        $id_dpto = $row->id_dpto;
    } else {
        echo "<script>alert('sector industrial no encontrado');</script>";
        exit;
    }

    //consulta para conseguir dpto
    $query = $conexion->prepare("SELECT nom_dpto FROM np_deptos WHERE id_dpto = ?");
    $query->bind_param("i", $id_dpto);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_object()) {
        $dpto = $row->nom_dpto;
    } else {
        echo "<script>alert('departamento no encontrado');</script>";
        exit;
    }

    //consulta para conseguir contactos para el proyecto
    $query = $conexion->prepare("SELECT id_contacto, nom_contacto, tel_contacto, email FROM nm_contactos WHERE id_contacto = ?");
    $query->bind_param("i", $id_contacto);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_object()) {
        $nom_contacto = $row->nom_contacto;
        $tel_contacto = $row->tel_contacto;
        $email = $row->email;
    } 

    //consulta para conseguir tipo de entrega para el proyecto
    $query = $conexion->prepare("SELECT descrip FROM cp_incoterm WHERE id_incoterm = ?");
    $query->bind_param("i", $proyecto->cp_incoterm_Id_incoterm);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_object()) {
        $tipoEntrega = $row->descrip;
    } else {
        echo "<script>alert('tipo de entrega no encontrado');</script>";
        exit;
    }

    //consulta para conseguir pais de entrega para el proyecto
    $query = $conexion->prepare("SELECT nom_pais FROM np_paises WHERE id_pais = ?");
    $query->bind_param("i", $proyecto->np_paises_id_pais);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_object()) {
        $paisEntrega = $row->nom_pais;
    } else {
        echo "<script>alert('pais de entrega no encontrado');</script>";
        exit;
    }

    //consulta para conseguir tiempo de entrega para el proyecto
    $query = $conexion->prepare("SELECT time, Note FROM deliverytime WHERE id = ?");
    $query->bind_param("i", $proyecto->DeliveryTime_Id);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_object()) {
        $tiempoEntrega = $row->time;
        $note = $row->Note;
    } else {
        echo "<script>alert('tiempo de entrega no encontrado');</script>";
        exit;
    }

    //consulta para conseguir modo de pago para el proyecto
    $query = $conexion->prepare("SELECT descrip FROM vp_terminospago WHERE id_termino = ?");
    $query->bind_param("i", $proyecto->vp_terminospago_id_termino);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_object()) {
        $modoPago = $row->descrip;
    } else {
        echo "<script>alert('modo de pago no encontrado');</script>";
        exit;
    }

    //consulta para conseguir validez de la oferta para el proyecto
    $query = $conexion->prepare("SELECT days, Note FROM offervalidity WHERE id = ?");
    $query->bind_param("i", $proyecto->OfferValidity_Id);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_object()) {
        $validezOferta = $row->days;
        $note2 = $row->Note;
    } else {
        echo "<script>alert('validez de oferta no encontrado');</script>";
        exit;
    }
    // Consulta para obtener las exclusiones seleccionadas para este proyecto
    $selectedExclusions = [];
    $querySelected = "SELECT exclusion_id FROM Projects_Exclusions WHERE projects_id = ?";
    if ($stmtSelected = $conexion->prepare($querySelected)) {
        $stmtSelected->bind_param("i", $id);
        $stmtSelected->execute();
        $resultSelected = $stmtSelected->get_result();

        while ($rowSelected = $resultSelected->fetch_assoc()) {
            $selectedExclusions[] = $rowSelected['exclusion_id'];
        }
        $stmtSelected->close();
    }

    ?>
    <div class="container-fluid mt-3">
        <h5>Información del Proyecto</h5>
        <div class="row mb-2">
            <div class="col-auto">
                <label class="label-nav">
                    <strong>Número:</strong>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <?= htmlspecialchars($searchQuery) ?>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <strong>Nombre del Proyecto:</strong>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <?= $proyecto ? htmlspecialchars($proyecto->Name) : 'No definido' ?>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <strong>Divisa:</strong>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <?= htmlspecialchars($currency) ?>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <strong>Centro de Costo:</strong>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <?= $proyecto ? htmlspecialchars($proyecto->CostCenter) : 'No definido' ?>
                </label>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-auto">
                <label class="label-nav">
                    <strong>Fecha y Hora:</strong>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <?= $fechaProyecto ?>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <strong>Cliente:</strong>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <?= htmlspecialchars($razonSocial) ?>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <strong>Sector Industrial:</strong>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <?= $sectorIndustrial ?? 'No definido' ?>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <strong>Ciudad:</strong>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <?= htmlspecialchars($ciudad) ?>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <strong>Departamento:</strong>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <?= $dpto ?? 'No definido' ?>
                </label>
            </div>
        </div>
        <h5>Contactos para el Proyecto</h5>
        <div class="row mb-2">
            <div class="col-auto">
                <label class="label-nav">
                    <strong>Nombre:</strong>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <?= htmlspecialchars($nom_contacto) ?>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <strong>Celular:</strong>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <?= htmlspecialchars($tel_contacto) ?>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <strong>Correo:</strong>
                </label>
            </div>
            <div class="col-auto">
                <label class="label-nav">
                    <?= htmlspecialchars($email) ?>
                </label>
            </div>
        </div>
        <div class="row mb-2 justify-content-end">
            <div class="col-auto">
                <label for="costo" class="form-label">Costo:</label>
            </div>
            <?php
            $totalPrice = 0;
            $totalCurrency = '';
            // Consulta preparada para obtener el precio con la divisa 
            $query = "
                SELECT 
                ep.price AS 'precio',
                am.currency AS 'divisa'
            FROM EquipmentsPrices ep
            JOIN Equipments e ON ep.equipments_id = e.id
            JOIN am_monedas am ON ep.am_monedas_id = am.id
            WHERE am.id = ? AND ep.projects_equipments_id = ?
            ";

            // Preparar y ejecutar la consulta
            if ($stmt = $conexion->prepare($query)) {
                $stmt->bind_param("ii", $proyecto->am_monedas_id, $searchQuery); // Vincular los parámetros
                $stmt->execute();
                $result = $stmt->get_result();

                // Sumar los precios de todos los equipos seleccionados para el proyecto específico
                while ($datos = $result->fetch_object()) {
                    $totalPrice += $datos->precio; // Acumular los precios
                    $totalCurrency = $datos->divisa; // Obtener la divisa seleccionada
                }
            }
            ?>
            <div class="col-auto">
                <label class="label-nav">
                    <?= htmlspecialchars($totalPrice) ?> <?= htmlspecialchars($totalCurrency) ?>
                </label>
            </div>
        </div>

        <h5>Bienes y Servicios para el Proyecto</h5>
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th scope="col">Servicio elegido</th>
                    <th scope="col">Detalle del servicio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = $conexion->query("SELECT 
                s.description AS 'Servicioelegido',
                CONCAT(c.description, ' ', c.quiantity) AS 'Detalledelservicio'
                FROM 
                Projects_Services ps
                JOIN Services s ON ps.services_id = s.id
                JOIN Complements c ON s.complements_id = c.id
                WHERE 
                ps.projects_id = $searchQuery
                GROUP BY 
                s.description, CONCAT(c.description, ' ', c.quiantity);");
                while ($datos = $sql->fetch_object()) { ?>
                    <tr>
                        <td><?= $datos->Servicioelegido ?></td>
                        <td><?= $datos->Detalledelservicio ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h5>Equipos para el Proyecto</h5>
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
                JOIN Equipments e ON pe.equipments_id = e.id
                JOIN Equipmentstypes t ON e.equipmentstypes_id = t.id
                WHERE 
                pe.projects_id = $searchQuery;");
                while ($datos = $sql->fetch_object()) { ?>
                    <tr>
                        <td><?= $datos->descripcion ?></td>
                        <td><?= $datos->marca ?></td>
                        <td><?= $datos->modelo ?></td>
                        <td><?= $datos->referencia ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h5>Condiciones del Proyecto</h5>
        <div class="row">
            <div class="col-auto">
                <label class="label-nav"><strong>Exclusiones:</strong></label>      
            </div>
            <div class="col-auto">
                <?php
                // Consulta para obtener las exclusiones
                $query = "SELECT id, description FROM exclusions";
                $result = $conexion->query($query);

                while ($row = $result->fetch_object()) {
                    $isChecked = in_array($row->id, $selectedExclusions) ? 'checked' : ''; // Verificar si esta exclusión está seleccionada
                    echo '<div class="form-check d-flex justify-content-between">';
                    echo '<label class="form-check-label flex-grow-1 exclusion-label" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="' . htmlspecialchars($row->description) . '">';
                    echo htmlspecialchars($row->description);
                    echo '</label>';
                    echo '<input class="form-check-input ms-2" type="checkbox" ' . $isChecked . ' disabled>'; // Solo lectura
                    echo '</div>';
                }
                ?>
            </div>
            <div class="col-auto">
                <div class="row mb-2">
                    <div class="form-group">
                        <label class="label-nav"><strong>Tipo Entrega: </strong></label>
                        <label class="label-nav"><?= $tipoEntrega ?></label>
                    </div>
                    <div class="form-group">
                        <label class="label-nav"><strong>País Entrega: </strong></label>
                        <label class="label-nav"><?= $paisEntrega ?? 'No definido' ?></label>   
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group">
                    <label class="label-nav"><strong>Tiempo Entrega: </strong></label>
                    <label class="label-nav"><?= $tiempoEntrega ?> <?= $note ?></label>  
                </div>
                <div class="form-group">
                    <label class="label-nav"><strong>Modo de Pago: </strong></label>
                    <label class="label-nav"><?= $modoPago ?? 'No definido' ?></label>
                </div>
                <div class="form-group">
                    <label class="label-nav"><strong>Validez de la Oferta:</strong></label>
                    <label class="label-nav"><?= $validezOferta ?> <?= $note2 ?></label>
                </div>
            </div>
        </div>
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