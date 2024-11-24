<?php
// Conexión a la base de datos
include "scripts/conexion.php";

if (isset($_GET['id_contacto'])) {
    $idContacto = intval($_GET['id_contacto']); // Asegurarse de que sea un entero
    $query = "SELECT tel_contacto, email FROM nm_contactos WHERE id_contacto = $idContacto";
    $result = $conexion->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode([
            "tel_contacto" => $row['tel_contacto'],
            "email" => $row['email']
        ]);
    } else {
        echo json_encode([
            "tel_contacto" => null,
            "email" => null
        ]);
    }
} else {
    echo json_encode([
        "error" => "No se proporcionó un ID de contacto"
    ]);
}
?>
