<?php
include "scripts/conexion.php";

if (isset($_GET['marcaId'])) {
    $marcaId = (int) $_GET['marcaId'];


    // Consulta para obtener el precio basado en marca, modelo y referencia
    $query = "
       SELECT ep.price, am.currency
        FROM EquipmentsPrices ep
        JOIN Equipments e ON ep.equipments_id = e.id
        JOIN am_monedas am ON ep.am_monedas_id = am.id
        WHERE e.id = $marcaId 
    ";
    $result = $conexion->query($query);

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode([
            'price' => $data['price'],
            'currency' => $data['currency']
        ]);
    } else {
        echo json_encode(['error' => 'No se encontró el precio.']);
    }
} else {
    echo json_encode(['error' => 'Parámetros incompletos.']);
}
