<?php
$servidor = stream_socket_server('tcp://0.0.0.0:9000', $codigo, $error);
echo "Servidor (select) escuchando en el puerto 9000\n";

$clientes = [];

while (true) {
    $lectura = $clientes;
    $lectura[] = $servidor;
    $escritura = null;
    $excepciones = null;

    stream_select($lectura, $escritura, $excepciones, null);
    //recorre los sockets
    foreach ($lectura as $socket) {
        //si llega un nuevo cliente se agrega a la lista
        if ($socket === $servidor) {
            $nuevo = stream_socket_accept($servidor);
            $clientes[] = $nuevo;
            echo "Se conectó: " . stream_socket_get_name($nuevo, true) . "\n";
            fwrite($nuevo, "Hola, soy el servidor\n");
            continue;
        }
        //se envia la IP y el puerto. cada vez que sea pedida la petición se utiliza
        $quien = stream_socket_get_name($socket, true);
        $linea = fgets($socket);

        if ($linea === false) {
            echo "Se desconectó: $quien\n";
            unset($clientes[array_search($socket, $clientes, true)]);
            fclose($socket);
            continue;
        }

        $linea = trim($linea);
        echo "[$quien] $linea\n";
        fwrite($socket, strtoupper($linea) . "\n");
    }
}