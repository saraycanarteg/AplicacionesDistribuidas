<?php
const PUERTO = 8007;

$conexion = stream_socket_client('tcp://127.0.0.1:' . PUERTO, $codigo, $error, 5);
if (!$conexion) {
    die("no se pudo conectar al servidor: $error\n");
}
echo "Conectado al servidor\n";

while (true) {
    $lectura = [STDIN, $conexion];
    $escritura = null;
    $excepciones = null;

    stream_select($lectura, $escritura, $excepciones, null);

    //se envia al servidor lo q escribe el usuario
    if (in_array(STDIN, $lectura, true)) {
        $linea = fgets(STDIN);
        if ($linea === false) {
            break;
        }
        fwrite($conexion, trim($linea) . "\n");
    }

    //mostrar respuestas del servidor
    if (in_array($conexion, $lectura, true)) {
        $mensaje = fgets($conexion);
        if ($mensaje === false) {
            echo "El servidor cerró la conexión\n";
            break;
        }
        echo $mensaje;
    }
}

fclose($conexion);
