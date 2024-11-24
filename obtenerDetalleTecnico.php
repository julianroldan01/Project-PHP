<?php
include "scripts/conexion.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = $conexion->prepare("
        SELECT 
            ep.nameparameter AS parametro,
            ep.value AS valor,
            ep.unit AS unidad
        FROM 
            equipmentsparameters ep
        JOIN 
            equipments e ON ep.equipments_id = e.id
        WHERE 
            e.id = ?
    ");
    $sql->bind_param("i", $id);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        echo '<table class="table table-striped">';
        echo '<thead><tr><th>Parámetro</th><th>Valor</th><th>Unidad</th></tr></thead>';
        echo '<tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['parametro']) . '</td>';
            echo '<td>' . htmlspecialchars($row['valor']) . '</td>';
            echo '<td>' . htmlspecialchars($row['unidad']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No se encontraron detalles técnicos para esta marca.</p>';
    }
} else {
    echo '<p>Error: No se recibió el identificador de la marca.</p>';
}
?>
