<?php

$conexion = stream_socket_client('tcp://localhost:9000', $codigo, $error, 5);
echo "Conectando al servidor \n";

/*$mensaje = fgets($conexion);
echo "El servidor dice: $mensaje \n";
*/

echo "El servidor dijo: ".fgets($conexion); //revisa la conexion

//configurar las peticiones de varios clientes
while(( $linea = fgets(STDIN) !== false )){ //stdin para poder escribir
    fwrite($conexion, $linea);
    echo "La respuesta es: ".fgets($conexion);
} 

fclose($conexion);

?>