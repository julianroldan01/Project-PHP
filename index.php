<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cotizaciones</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="./style/style.css">
</head>

<body>

  <nav class="navbar">
    <a href="index.php">Cotizaciones:</a>
    <li><a href="">Administrar</a>
      <ul>
        <li><a href="parametricas.php">Tablas Paramétricas</a></li>
        <li><a href="tablasasociadas.php">Tablas Asociadas</a></li>
      </ul>
    </li>
    <li><a href="">Gestionar</a>
      <ul>
        <?php
        include "scripts/conexion.php";

        $result = $conexion->query("SELECT nm_juridicas_numid FROM projects ORDER BY id DESC LIMIT 1");

        if ($result && $result->num_rows > 0) {
          $lastNumber = $result->fetch_object()->nm_juridicas_numid + 1; // Siguiente número disponible
        } else {
          $lastNumber = 1; // Si no hay registros aún, empieza en 1
        }
        ?>
        <!-- Puedes usar $lastNumber directamente aquí -->
        <li>
          <?php
          include "scripts/conexion.php";
          include "scripts/scripts.php";

          $result = $conexion->query("SELECT nm_juridicas_numid FROM projects ORDER BY id DESC LIMIT 1");

          if ($result && $result->num_rows > 0) {
            $lastNumber = $result->fetch_object()->nm_juridicas_numid + 1; // Siguiente número disponible
          } else {
            $lastNumber = 1; // Si no hay registros aún, empieza en 1
          }
          ?>
          <a href="crear.php?id=<?= $lastNumber ?>">Crear</a>
        </li>
        <li><a href="modificar.php">Modificar</a></li>
        <li><a href="visualizar.php">Visualizar</a></li>
      </ul>
    </li>
    <button id="contenidoArchivo" class="c"></button>

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
    <a class="image-button" href="javascript:history.back()"">
                <img src=" ./images/atras.png" alt="">
    </a>
  </nav>
  <div class="cubo-soft">
    <span>.... . . . . CUBO Soft</span>
  </div>
  <div class="linea-izquierda"></div>
  <div class="linea-abajo"></div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>