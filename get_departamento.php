<?php 
// Conexión a la base de datos
include "scripts/conexion.php";

if (isset($_GET['ciudadId'])) {
    $ciudadId = intval($_GET['ciudadId']); // Convertir a entero por seguridad
    $query = "
       SELECT dp.nom_dpto AS departamento
       FROM np_ciudades c
       JOIN np_deptos dp ON c.id_dpto = dp.id_dpto
       WHERE c.id_ciudad = $ciudadId
    ";
    $result = $conexion->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        echo htmlspecialchars($row['departamento']); // Devolver el nombre del departamento
    } else {
        echo "Departamento no encontrado"; // Manejar caso de no encontrar resultados
    }
} else {
    echo "Error: ciudadId no proporcionado"; // Manejar error de solicitud
}
?>
