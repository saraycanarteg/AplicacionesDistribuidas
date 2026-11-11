<?php

//se crea variable servidor con acceso a todas las IPs en el puerto 900
$servidor = stream_socket_server('tcp://0.0.0.0:9000', $codigo, $error);
echo "Servidor escuchando en el puerto 9000\n";

/*$cliente = stream_socket_accept($servidor, -1, $quien);
echo "Se conectó: $quien \n";

fwrite($cliente, "Hola, soy el servidor\n");

fclose($cliente);
*/
while(true){
    $cliente = stream_socket_accept($servidor, -1, $quien);
    echo "Se conectó: $quien \n";
    fwrite($cliente, "Hola, soy el servidor\n");

    //para verficar y esperar la escritura del cliente
    while(($linea =fgets($cliente)) !== false){
        $linea = trim($linea);
        echo "[$quien] $linea \n";

        fwrite ($cliente, strtoupper($linea). "\n"); //responde en mayuscula
    }

  fclose($cliente);  
  echo "Se desconectó $quien \n";
}