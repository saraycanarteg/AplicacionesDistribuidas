<?php
$servidor = stream_socket_server('tcp://0.0.0.0:9000', $codigo, $error);
echo "Servidor (select) escuchando en el puerto 9000\n";

$clientes = [];
$precio = 100;
$lider = 'nadie';

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
        /*
        $linea = trim($linea);
        echo "[$quien] $linea\n";
        fwrite($socket, strtoupper($linea) . "\n");
        */
        
        $linea = trim($linea);
        echo "[$quien] $linea\n";

        /*foreach ($clientes as $otro) {
            if ($otro !== $socket) {
                fwrite($otro, "[$quien] $linea\n");
            }
        }*/
        $partes = explode(' ', $linea);
        $comando = strtoupper($partes[0]);

        if ($comando === 'PRECIO') {
            fwrite($socket, "PRECIO $precio $lider\n");
        } elseif ($comando === 'PUJAR' && count($partes) === 2 && is_numeric($partes[1])) {
            $monto = (float) $partes[1];
            if ($monto <= $precio) {
                fwrite($socket, "ERROR la puja debe superar $precio\n");
            } else {
                $precio = $monto;
                $lider = $quien;
                fwrite($socket, "OK puja aceptada: $monto\n");
                foreach ($clientes as $otro) {
                    if ($otro !== $socket) {
                        fwrite($otro, "NUEVA $monto $lider\n");
                    }
                }
            }
        } else {
            fwrite($socket, "ERROR use PRECIO o PUJAR monto\n");
        }

    }
}