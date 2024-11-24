<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cotizaciones</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="./style/style.css">
</head>
<?php
ob_start(); // Inicia el buffer de salida

include "scripts/conexion.php";
include "scripts/scripts.php";

// Captura los datos del formulario
$searchBy = isset($_GET['searchBy']) ? $_GET['searchBy'] : ''; // 'numero' o 'nombre'
$searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : ''; // El valor ingresado en el input

// Verifica que los datos necesarios estén presentes
if ($searchBy && $searchQuery) {
  // Validaciones adicionales para asegurarse de que el término de búsqueda es adecuado
  if ($searchBy === 'numero') {
    // Verifica que el término de búsqueda sea un número válido
    if (!is_numeric($searchQuery)) {
      // Alerta de error y no redirigir
      echo "<script>alert('Por favor ingrese un número válido para buscar por número.');</script>";
      echo "<a href='modificar.php'>Volver a intentar</a>";
      exit(); // Detiene la ejecución si la validación falla
    }
  } elseif ($searchBy === 'nombre') {
    // Verifica que el término de búsqueda contenga solo letras, números y espacios (para el nombre de proyecto)
    if (!preg_match("/^[a-zA-Z0-9\s]+$/", $searchQuery)) {
      // Alerta de error y no redirigir
      echo "<script>alert('Por favor ingrese un nombre válido (solo letras, números y espacios) para buscar por nombre.');</script>";
      echo "<a href='modificar.php'>Volver a intentar</a>";
      exit(); // Detiene la ejecución si la validación falla
    }
  }

  // Guardar el valor original antes de modificarlo
  $searchQueryOriginal = $searchQuery;

  // Prepara la consulta SQL según el tipo de búsqueda
  if ($searchBy === 'numero') {
    // Buscar por número (suponiendo que 'nm_juridicas_numid' es el campo que contiene el número)
    $query = "SELECT * FROM Projects WHERE nm_juridicas_numid LIKE ?";
  } else {
    // Buscar por nombre de proyecto (suponiendo que 'Name' es el campo que contiene el nombre)
    $query = "SELECT * FROM Projects WHERE Name LIKE ?";
  }

  // Prepara la sentencia SQL
  if ($stmt = $conexion->prepare($query)) {
    $searchQuery = "$searchQuery"; // Se usa '%' para la búsqueda parcial
    $stmt->bind_param('s', $searchQuery); // 's' para cadena

    // Ejecuta la consulta
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica si se encontraron resultados
    if ($result->num_rows > 0) {
      // Si se encuentran resultados, obtiene el primer resultado
      $row = $result->fetch_assoc();
      // Redirige a la página de modificación con el valor original (sin '%' ni modificaciones)
      header("Location: modificarview.php?searchBy=$searchBy&searchQuery=$searchQueryOriginal");
      exit();
    } else {
      // Si no se encuentran resultados, muestra un mensaje y evita la redirección
      echo "<script>alert('No se encontraron resultados para su búsqueda.');</script>";
      echo "<a href='modificar.php'>Volver a intentar</a>";
      exit();
    }

    // Cierra la conexión
    $stmt->close();
  } else {
    echo "Error al preparar la consulta.<br>";
  }
}

ob_end_flush(); // Envía la salida del buffer
?>

<body>
  <nav class="navbar">
    <a href="index.php">Cotizaciones:</a>
    <li class="nav-item dropdown">
      <a class="nav-link" href="modificar.php" role="button" aria-expanded="false">
        Modificar
      </a>
    </li>
    <button id="contenidoArchivo" class="c"></button>
    <a class="image-button" href="javascript:history.back()">
      <img src="./images/atras.png" alt="">
    </a>
  </nav>
  <form id="searchForm" action="modificar.php" method="GET">
    <div class="d-flex justify-content-center align-items-center vh-100">
      <div class="custom-card">
        <div class="card-body">
          <h2 class="card-title text-start mb-4 label-nav">Buscar cotización por:</h2>
          <div class="row align-items-center">
            <div class="col-auto">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="searchBy" id="byNumber" value="numero" checked>
                <label class="form-check-label label-nav" for="byNumber">Número</label>
              </div>
            </div>
            <div class="col-auto">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="searchBy" id="byName" value="nombre">
                <label class="form-check-label label-nav" for="byName">Nombre de Proyecto</label>
              </div>
            </div>
          </div>

          <div class="row align-items-center mt-3">
            <div class="col-8">
              <input type="text" class="form-control custom-input" name="searchQuery" placeholder="Ingrese número o nombre de proyecto" required>
            </div>
            <div class="col-auto">
              <button type="submit" class="image-button2">
                <img src="/images/buscar.png" alt="Botón de Imagen" class="button-image3">
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <div class="cubo-soft">
    <span>.... . . . . CUBO Soft</span>
  </div>
  <div class="linea-izquierda"></div>
  <div class="linea-abajo"></div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>