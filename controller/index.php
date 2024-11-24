<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>

<nav class="navbar">
<a href="C:/xampp/htdocs/EntregaTckt1016B/index.html">Cotizaciones:</a>
<li><a href="">Administrar</a>
    <ul>
        <li><a href="http://localhost/EntregaTckt1016B/crear.php">Crear</a></li>
        <li><a href="http://localhost/EntregaTckt1016B/modificar.php">Modificar</a></li>
        <li><a href="http://localhost/EntregaTckt1016B/visualizar.php">Visualizar</a></li>
    </ul>
</li>
<li><a href="">Gestionar</a></li>
        <button class="c">Cliente Corp</button>
        <a class="image-button" href="#">
                <img src="./images/atras.png" alt="">
        </a>
</nav>
    <div class="container-fluid row">
        <form class="col-4">
            <div class="mb-3 p-3">
                <h3 class="text-center text-secondary">Información del proyecto</h3>
                <label for="exampleInputEmaill" class="form-label">Nombre de la persona</label>
                <input type="text" class="form-control" name="nombre">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmaill" class="form-label">Apellido de la persona</label>
                <input type="text" class="form-control" name="apellido">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmaill" class="form-label">cedula de la persona</label>
                <input type="text" class="form-control" name="cedula">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmaill" class="form-label">Fecha</label>
                <input type="date" class="form-control" name="fecha">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmaill" class="form-label">Correo</label>
                <input type="text" class="form-control" name="correo">
            </div>
            <button type="submit" class ="btn btn-primary" name="btnregistrar" value="ok">Registrar</button>
        </form>
        <div class="col-8 p-4">
                    <table class="table">
            <thead>
                <tr>
                <h3 class="text-center text-secondary">Informacion del proyecto</h3>
                <th scope="col">Numero</th>
                <th scope="col">Nombre del proyecto</th>
                <th scope="col">Divisa</th>
                <th scope="col">Centro de costo</th>
                <th scope="col">Fecha y hora</th>
                <th scope="col">Cliente</th>
                <th scope="col">Sector industrial</th>
                <th scope="col">Ciudad</th>
                <th scope="col">Departamento</th>
                <th scope="col">Nombre</th>
                <th scope="col">Celular</th>
                <th scope="col">Correo</th>
                <th scope="col">Exclusiones</th>
                <th scope="col">Tipo entrega</th>
                <th scope="col">Pais Entrega</th>
                <th scope="col">Tiempo entrega</th>
                <th scope="col">Modo de pago</th>
                <th scope="col">Validez de la oferta</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
                </tr>
                <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
                </tr>
                <tr>
                <th scope="row">3</th>
                <td colspan="2">Larry the Bird</td>
                <td>@twitter</td>
                </tr>
            </tbody>
            </table>
        </div>
    </div>
    <div class="footer">
    <span>.... . . . . CUBO Soft</span>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>