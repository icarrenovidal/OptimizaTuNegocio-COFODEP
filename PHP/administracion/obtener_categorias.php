<?php
include __DIR__ . '/../../Config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT id_categoria, nombre FROM categorias_productos ORDER BY nombre ASC";
$result = $conexion->query($sql);
$categorias = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = [
            'id_categoria' => $row['id_categoria'],
            'nombre' => $row['nombre']
        ];
    }
}

echo json_encode($categorias, JSON_UNESCAPED_UNICODE);
