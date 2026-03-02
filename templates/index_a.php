<?php
// index_a.php
// -------------------------------------------------------------
// Formulario de prueba para enviar datos manuales al controlador
// Symfony ubicado en: http://10.208.36.97/seiscomp/public/informe/
// -------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Envío a /informe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 40px;
        }
        table {
            border-collapse: collapse;
            width: 95%;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        th {
            background: #e9ecef;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-align: center;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 7px 14px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        h2 {
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>

<h2>Formulario de Envío de Informes hacia Symfony (/informe)</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Magnitud</th>
            <th>Latitud</th>
            <th>Longitud</th>
            <th>Informe</th>
            <th>Epicentro</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <tr>
            <form action="http://10.208.36.97/seiscomp/public/informe/" method="post">
                <td><input type="text" name="id" placeholder="Ej: <?= $i ?>"></td>
                <td><input type="date" name="fecha" placeholder="AAAA-MM-DD"></td>
                <td><input type="number" step="0.1" name="mag" placeholder="Ej: 5.4"></td>
                <td><input type="number" step="0.001" name="lat" placeholder="Ej: 9.935"></td>
                <td><input type="number" step="0.001" name="long_" placeholder="Ej: -84.091"></td>
                <td><input type="text" name="informe" placeholder="Tipo de informe"></td>
                <td><input type="text" name="epi" placeholder="Ej: San José"></td>
                <td><button type="submit">Enviar</button></td>
            </form>
        </tr>
    <?php endfor; ?>
    </tbody>
</table>

</body>
</html>
