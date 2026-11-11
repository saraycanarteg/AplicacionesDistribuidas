<?php
$conexion = stream_socket_client('tcp://localhost:9000', $codigo, $error, 5);
if (!$conexion) {
    die("no se pudo conectar a la calculadora, revise que servidor.php este ejecutandose ($error)\n");
}
echo fgets($conexion);

while (true) {
    //leer la operacion del teclado
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
    //mostrar respuesta
    $respuesta = trim($respuesta);
    echo $respuesta . "\n";

    if ($respuesta === 'OK adios') {
        break;
    }
}

fclose($conexion);
echo "conexion terminada\n";
