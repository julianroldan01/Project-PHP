<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./style/style.css">
</head>

<body>
    <nav class="navbar">
        <a href="index.php">Administración:</a>
        <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Tablas Paramétricas
      </a>
      <ul class="dropdown-menu">
        <?php
        include "scripts/conexion.php";
        include "scripts/scripts.php"; 

        $sql = $conexion->query("SELECT bt.id, bt.name
FROM BaseTables bt
JOIN AsociateTables at ON bt.id = at.basetables_id
GROUP BY bt.id
HAVING COUNT(at.basetables_id) = 1;");
        while ($datos = $sql->fetch_object()) { ?>
          <li>
            <a class="dropdown-item" href="parametricas_detalle.php?id=<?= $datos->id ?>">
              <?= $datos->name ?>
            </a>
          </li>
        <?php } ?>
      </ul>
    </li>
        <button id="contenidoArchivo" class="c"></button>
        <a class="image-button" href="javascript:history.back()">
            <img src="./images/atras.png" alt="">
        </a>
    </nav>

    <!-- Contenedor centrado -->
    <?php
include "scripts/conexion.php"; // Asegúrate de incluir la conexión a la base de datos

// Verificar si el parámetro 'id' está presente en la URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener el nombre de la tabla de la base de datos
    $sql = $conexion->query("SELECT id, name FROM basetables WHERE id = $id");

    if ($sql && $sql->num_rows > 0) {
        $service = $sql->fetch_object();
        $table_name = $service->name;
    } else {
        $error_message = "Base de datos no encontrada";
    }
} else {
    $error_message = "Seleccione una base de datos";
}

// Consultar todos los registros de la tabla seleccionada
$records = [];
if (isset($table_name)) {
    $records_sql = $conexion->query("SELECT * FROM $table_name ORDER BY id");
    if ($records_sql && $records_sql->num_rows > 0) {
        while ($record = $records_sql->fetch_assoc()) {
            $records[] = $record; // Guardamos todos los registros
        }
    }
}

// Manejo de la navegación entre registros
$current_record = isset($_GET['current_record']) ? $_GET['current_record'] : 0;
$total_records = count($records);

// Si el índice está fuera del rango de registros, se ajusta
if ($current_record < 0) $current_record = 0;
if ($current_record >= $total_records) $current_record = $total_records - 1;

// Obtener el registro actual para mostrar en el formulario
$current_data = isset($records[$current_record]) ? $records[$current_record] : null;

// Procesar el formulario cuando se envíe para agregar un nuevo registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    // Recoger los valores de los inputs
    $data = [];
    foreach ($_POST as $key => $value) {
        if ($key !== 'guardar') {
            $data[$key] = mysqli_real_escape_string($conexion, $value);
        }
    }

    // Preparar la consulta de inserción para la tabla seleccionada
    if (isset($table_name)) {
        // Obtener las columnas de la tabla seleccionada
        $columns_sql = $conexion->query("DESCRIBE $table_name");
        $columns = [];
        while ($column = $columns_sql->fetch_assoc()) {
            $columns[] = $column['Field']; // Guardamos los nombres de las columnas
        }

        // Verificar si los datos enviados corresponden a los campos de la tabla
        $columns_list = implode(", ", $columns);
        $values_list = "'" . implode("', '", array_intersect_key($data, array_flip($columns))) . "'";

        // Preparar la consulta de inserción
        $insert_query = "INSERT INTO $table_name ($columns_list) VALUES ($values_list)";

        if ($conexion->query($insert_query)) {
            $success_message = "Datos guardados exitosamente en la tabla '$table_name'.";
            // Actualizar la lista de registros después de agregar el nuevo
            $records_sql = $conexion->query("SELECT * FROM $table_name ORDER BY id");
            $records = [];
            while ($record = $records_sql->fetch_assoc()) {
                $records[] = $record;
            }
        } else {
            $error_message = "Error al guardar los datos: " . $conexion->error;
        }
    }
}
?>

<div class="container-centered row mb-1">
    <h6 class="label-nav d-flex justify-content-center col-12">Tabla:
        <?php
        if (isset($service)) {
            echo htmlspecialchars($service->name);
            // Obtener los campos de la tabla seleccionada
            $columns_sql = $conexion->query("DESCRIBE $table_name");
            $columns = [];
            while ($column = $columns_sql->fetch_assoc()) {
                $columns[] = $column['Field']; // Guardamos los nombres de las columnas
            }
        } else {
            echo isset($error_message) ? $error_message : "Seleccione una base de datos";
        }
        ?>
    </h6>

    <!-- Formulario -->
    <form action="" method="POST">
        <div class="table-container col-12">
            <table class="table table-bordered">
                <thead class="bg-gray">
                    <tr>
                        <th>CAMPO</th>
                        <th>VALOR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($columns)) {
                        foreach ($columns as $column) {
                            // Si hay un registro actualx, prellenar el valor
                            $value = isset($current_data[$column]) ? $current_data[$column] : '';
                            echo "<tr>
                                    <td>" . htmlspecialchars($column) . "</td>
                                    <td><input type='text' name='$column' value='$value' class='form-control1'></td>
                                </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Balance de registros -->
        <div class="record-info">
            <span class="label-nav">Registro <span id="currentRecord"><?= $current_record + 1 ?></span> / <span id="totalRecords"><?= $total_records ?></span></span>
        </div>

        <!-- Controles de navegación -->
        <div class="navigation-buttons">
            <a href="?id=<?= $id ?>&current_record=<?= max(0, $current_record - 1) ?>" class="btn-transparent me-2">◀</a>
            <a href="?id=<?= $id ?>&current_record=<?= min($total_records - 1, $current_record + 1) ?>" class="btn-transparent me-2">▶</a>
            <button type="button" onclick="resetForm()" class="btn-transparent me-2">⏺</button>
        </div>

        <!-- Botón de guardar (más a la derecha) -->
        <div class="save-button-container d-flex justify-content-end col-9">
            <button type="submit" name="guardar" class="btn-guardar">
                <img src="/images/Picture3.png" alt="Guardar" class="icono-boton">
            </button>
        </div>
    </form>

    <?php
    // Mostrar mensajes de error o éxito
    if (isset($success_message)) {
        echo "<div class='alert alert-success'>$success_message</div>";
    } elseif (isset($error_message)) {
        echo "<div class='alert alert-danger'>$error_message</div>";
    }
    ?>
</div>

    <div class="cubo-soft">
        <span>.... . . . . CUBO Soft</span>
    </div>
    <div class="linea-izquierda"></div>
    <div class="linea-abajo"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>