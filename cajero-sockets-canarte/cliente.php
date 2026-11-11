<?php
//crear y comprobar conexion
$conexion = stream_socket_client('tcp://localhost:9000', $codigo, $error, 5);
if (!$conexion) {
    die("no se pudo conectar al cajero: $error\n");
}
//mostrar mensae servidor
echo fgets($conexion);

while (true) {
    //leer lo q escribe cliente
    echo "> ";
    $linea = fgets(STDIN);
    if ($linea === false) {
        break;
    }
    //enviar al servidor 
    fwrite($conexion, trim($linea) . "\n");
    $respuesta = fgets($conexion);
    if ($respuesta === false) {
        echo "el servidor cerro la conexion\n";
        break;
    }
    //mostrar respuesta del servidor en el cliente
    $respuesta = trim($respuesta);
    echo $respuesta . "\n";

    if ($respuesta === 'ADIOS') {
        break;
    }
}

fclose($conexion);
