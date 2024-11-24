<?php
include "scripts/conexion.php"; // Incluir la conexión a la base de datos

if (isset($_GET['divisaId']) && isset($_GET['projectEquipmentsId'])) {
    $divisaId = (int) $_GET['divisaId'];
    $projectEquipmentsId = (int) $_GET['projectEquipmentsId']; // ID de project_equiments_id

    // Consultar los precios y la divisa seleccionada, filtrando por el project_equiments_id
    $totalPrice = 0;
    $totalCurrency = '';

    // Consulta preparada para obtener el precio con la divisa seleccionada y filtrado por project_equiments_id
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
        $stmt->bind_param("ii", $divisaId, $projectEquipmentsId); // Vincular los parámetros
        $stmt->execute();
        $result = $stmt->get_result();

        // Sumar los precios de todos los equipos seleccionados para el proyecto específico
        while ($datos = $result->fetch_object()) {
            $totalPrice += $datos->precio; // Acumular los precios
            $totalCurrency = $datos->divisa; // Obtener la divisa seleccionada
        }

        // Devolver el precio total y la divisa en formato JSON
        echo json_encode([
            'success' => true,
            'precioTotal' => number_format($totalPrice, 2), // Formatear el precio
            'divisa' => $totalCurrency
        ]);

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No se ha seleccionado una divisa o un proyecto']);
}
?>
