<?php
// Conexión a la base de datos
include "scripts/conexion.php";

if (isset($_GET['clienteId'])) {
    $clienteId = intval($_GET['clienteId']);
    $query = "
        SELECT isec.Description AS sector_industrial
        FROM nm_juridicas nm
        JOIN IndustrialSectors isec ON nm.industrialsectors_id = isec.id
        WHERE nm.numid = $clienteId
    ";
    $result = $conexion->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        echo htmlspecialchars($row['sector_industrial']);
    } else {
        echo "Sector no encontrado";
    }
}
?>