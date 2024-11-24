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
<a href="index.php">Cotizaciones:</a>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Tablas Asociadas
    </a>
    <ul class="dropdown-menu">
        <?php
        include "scripts/conexion.php";
        include "scripts/scripts.php"; 
        $sql = $conexion->query("SELECT bt.id, bt.name
FROM BaseTables bt
JOIN AsociateTables at ON bt.id = at.basetables_id
GROUP BY bt.id
HAVING COUNT(at.basetables_id) > 1;
");
        while ($datos = $sql->fetch_object()) { ?>
            <li>
                <a class="dropdown-item" href="asociadas_detalle.php?id=<?= $datos->id ?>">
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

<div class="cubo-soft">
    <span>.... . . . . CUBO Soft</span>
</div>
<div class="linea-izquierda"></div>
<div class="linea-abajo"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>