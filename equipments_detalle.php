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
    <form method="POST">
    <?php
$id_project = isset($_GET['id_project']) ? intval($_GET['id_project']) : null;
$equipment_id = isset($_GET['id']) ? intval($_GET['id']) : null;

include "scripts/conexion.php";

if (isset($_POST['insertar_equipo'])) {
    // Validación inicial
    if (!$id_project || !$equipment_id) {
        echo "<script>alert('ID de proyecto o equipo inválido');</script>";
        exit;
    }

    // Obtener trademarc y ip_dtbasicos_sec_basico del equipo
    $query = $conexion->prepare("SELECT trademarc, ip_dtbasicos_sec_basico FROM equipments WHERE id = ?");
    $query->bind_param("i", $equipment_id);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_object()) {
        $ip_dtbasicos_sec_basico = $row->ip_dtbasicos_sec_basico;
    } else {
        echo "<script>alert('Equipo no encontrado');</script>";
        exit;
    }

    // Insertar en Projects_Equipments
    $sql = $conexion->prepare("INSERT INTO Projects_Equipments (projects_id, equipments_id, ip_dtbasicos_sec_basico) VALUES (?, ?, ?)");
    $sql->bind_param("iii", $id_project, $equipment_id, $ip_dtbasicos_sec_basico);

    if ($sql->execute()) {
        // Obtener el precio y la moneda del equipo
        $precioQuery = $conexion->prepare("
            SELECT ep.price, am.currency 
            FROM EquipmentsPrices ep 
            JOIN am_monedas am ON ep.am_monedas_id = am.id 
            WHERE ep.equipments_id = ?");
        $precioQuery->bind_param("i", $equipment_id);
        $precioQuery->execute();
        $precioResult = $precioQuery->get_result();

        if ($precioRow = $precioResult->fetch_assoc()) {
            $price = $precioRow['price'];
            $currency = $precioRow['currency'];

            // Obtener el ID de la moneda
            $currencyIdQuery = $conexion->prepare("SELECT id FROM am_monedas WHERE currency = ?");
            $currencyIdQuery->bind_param("s", $currency);
            $currencyIdQuery->execute();
            $currencyResult = $currencyIdQuery->get_result();

            if ($currencyRow = $currencyResult->fetch_assoc()) {
                $am_monedas_id = $currencyRow['id'];

                // Insertar en EquipmentsPrices
                $sqlPrice = $conexion->prepare("INSERT INTO EquipmentsPrices (equipments_id, projects_equipments_id, price, am_monedas_id) VALUES (?, ?, ?, ?)");
                $sqlPrice->bind_param("iddi", $equipment_id, $id_project, $price, $am_monedas_id);

                if ($sqlPrice->execute()) {
                    echo '<script type="text/javascript">window.location.href = "crear.php";</script>';
                } else {
                    echo "<script>alert('Error al insertar el precio del equipo: " . $conexion->error . "');</script>";
                }
            } else {
                echo "<script>alert('Moneda no encontrada');</script>";
            }
        } else {
            echo "<script>alert('No se pudo obtener el precio o la moneda');</script>";
        }
    } else {
        echo "<script>alert('Error al insertar el equipo: " . $conexion->error . "');</script>";
    }
}
?>
        <nav class="navbar">
            <a href="index.php">Equipos:</a>
            <label class="label-nav" id="selectedEquipmentsLabel">
                <?php
                include "scripts/scripts.php";
                if (isset($_GET['id'])) {
                    include "scripts/conexion.php";
                    $id = $_GET['id'];
                    $sql = $conexion->query("SELECT id,description FROM equipmentstypes WHERE id = $id");
                    if ($sql && $sql->num_rows > 0) {
                        $equipments = $sql->fetch_object();
                        echo htmlspecialchars($equipments->description);
                    } else {
                        echo "equipo no encontrado";
                    }
                } else {
                    echo "Seleccione un equipo";
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
            <div class="container-fluid mt-3 align-items-center">
                <div class="row mb-2 align-items-center">
                    <div class="col-auto">
                        <label for="marcaSelect" class="form-label">Marca:</label>
                    </div>
                    <?php
                    $query = "SELECT id, trademarc,ip_dtbasicos_sec_basico FROM equipments WHERE ip_dtbasicos_sec_basico=61 GROUP BY trademarc";
                    $result = $conexion->query($query);
                    ?>
                    <div class="col-auto">
                        <select name="trademarc" class="form-select transparent-select" id="marcaSelect" onchange="cargarDetalleTecnicoYPrecio()">
                            <option selected disabled>Selecciona la marca</option>
                            <?php while ($row = $result->fetch_object()) { ?>
                                <option value="<?= $row->id ?>"><?= htmlspecialchars($row->trademarc) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="cliente" class="form-label">Modelo:</label>
                    </div>
                    <?php
                    // Consulta para obtener los clientes
                    $query = "SELECT id, model FROM equipments WHERE ip_dtbasicos_sec_basico=61 GROUP BY model";
                    $result = $conexion->query($query);
                    ?>
                    <div class="col-auto">
                        <select name="model" class="form-select transparent-select" id="modeloSelect">
                            <option selected disabled>Selecciona el modelo</option>
                            <?php while ($row = $result->fetch_object()) { ?>
                                <option value="<?= $row->model ?>"><?= htmlspecialchars($row->model) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="cliente" class="form-label">Referencia:</label>
                    </div>
                    <?php
                    // Consulta para obtener los clientes
                    $query = "SELECT id, refernce FROM equipments WHERE ip_dtbasicos_sec_basico=61 GROUP BY refernce";
                    $result = $conexion->query($query);
                    ?>
                    <div class="col-auto">
                        <select name="refernce" class="form-select transparent-select" id="referenciaSelect">
                            <option selected disabled>Selecciona la referencia</option>
                            <?php while ($row = $result->fetch_object()) { ?>
                                <option value="<?= $row->refernce ?>"><?= htmlspecialchars($row->refernce) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <br>

            <div class="row mb-1">
                <div class="col-auto col-md-8">
                    <h4 class="label-nav">Detalle Técnico</h4>
                </div>
                <div class="col-auto">
                    <label for="costo" class="form-label">Precio:</label>
                </div>
                <div class="col-auto">
                    <label name="price" id="precioLabel" class="label-nav">Selecciona una opción</label>
                </div>
            </div>
            <div class="row mb-1 ">
                <div class="col-auto col-md-10">
                    <div id="detalleTecnico" class="mt-4">
                        <!-- Aquí se mostrarán los detalles técnicos dinámicamente -->
                    </div>
                </div>
                <div class="col-auto d-flex justify-content-end align-items-end">

                    <button type="submit" name="insertar_equipo" class="image-button2">
                        <img src="/images/Picture2.png" alt="Botón de Imagen" class="button-image2">
                    </button>
                </div>
            </div>
        </div>

        <div class="cubo-soft">
            <span>.... . . . . CUBO Soft</span>
        </div>
        <div class="linea-izquierda"></div>
        <div class="linea-abajo"></div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </form>
</body>

</html>