<?php
include "scripts/conexion.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'PUT') {
    // Leer y decodificar los datos de la solicitud
    $data = file_get_contents("php://input");
    $requestData = json_decode($data, true);

    if ($requestData !== null) {
        $padre = $requestData['padre'] ?? null;
        $id = $requestData['id'] ?? null;
        $field = $requestData['field'] ?? null;
        $table = $requestData['table'] ?? null;
        $data1 = $requestData['data'] ?? null;

        // Validar campos obligatorios
        if (empty($table) || empty($field) || empty($id) || empty($padre)) {
            echo json_encode([
                "error" => "Faltan valores requeridos",
                "detalles" => compact('table', 'field', 'id', 'padre'),
            ]);
            exit;
        }

        // Escapar nombres de tabla y campo para evitar problemas de inyección SQL
        $table = $conexion->real_escape_string($table);
        $field = $conexion->real_escape_string($field);

        // Construir la consulta SQL para la tabla principal
        $sql = "UPDATE `$table` SET `$field` = '$id' WHERE `id` = '$padre'";

        // Ejecutar la consulta principal
        $query = $conexion->query($sql);

        if (!$query) {
            echo json_encode([
                "error" => "Error en la actualización principal",
                "detalles" => $conexion->error,
            ]);
            exit;
        }

        // Si la primera actualización fue exitosa, proceder con la tabla relacionada
        if ($data1) {
            $relatedTable = str_replace('_id', '', $field);

            // Construir la parte SET de la consulta dinámicamente
            $setPart = '';
            foreach ($data1 as $column => $value) {
                if($column != "DateTime") {
                    $escapedValue = $conexion->real_escape_string($value);
                    $setPart .= "`$column` = '$escapedValue', ";
                }
            }
            $setPart = rtrim($setPart, ', ');
            print $setPart;

            // Consulta para actualizar la tabla relacionada
            $updateQuery = "UPDATE `$relatedTable` SET $setPart WHERE `id` = '$id'";
            $relatedUpdate = $conexion->query($updateQuery);

            if (!$relatedUpdate) {
                echo json_encode([
                    "error" => "Error en la actualización secundaria",
                    "detalles" => $conexion->error,
                ]);
                exit;
            }
        }

        // Respuesta exitosa
        echo json_encode(["resp" => "OK", "mensaje" => "Actualización realizada con éxito"]);
    } else {
        echo json_encode(["error" => "Datos de solicitud no válidos"]);
    }
} elseif ($method == 'GET') {
    if (isset($_GET['table'])) {
        $table = $_GET['table'];

        // Mapeo de tablas según alias
        $tableAliases = [
            'am' => 'am_monedas',
            'cities' => 'np_ciudades',
        ];
        $table = $tableAliases[$table] ?? $table;

        // Lista de tablas permitidas
        $allowedTables = ['equipmentstypes', 'equipments', 'am_monedas', 
            'conditions', 'projects', 'templatedocs', 'np_ciudades', 'industrialsectors', 'complements','projects_equipments'];

        // Validar si la tabla está permitida
        if (in_array($table, $allowedTables)) {
            $query = $conexion->query("SELECT * FROM `$table`");
            $records = [];

            if ($query) {
                while ($row = $query->fetch_assoc()) {
                    $records[] = $row;
                }
            } else {
                echo json_encode(["error" => "Error al consultar la tabla", "detalles" => $conexion->error]);
                exit;
            }

            // Respuesta con los registros en formato JSON
            echo json_encode($records);
        } else {
            echo json_encode(["error" => "Tabla no permitida", "tabla" => $table]);
        }
    } else {
        echo json_encode(["error" => "Tabla no especificada"]);
    }
} else {
    // Método HTTP no soportado
    echo json_encode(["error" => "Método HTTP no permitido"]);
}
?>
