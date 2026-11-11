<?php
const PUERTO = 8007;
const ASIENTOS = 7;

$servidor = stream_socket_server('tcp://0.0.0.0:' . PUERTO, $codigo, $error);
if (!$servidor) {
    die("no se pudo iniciar el servidor: $error\n");
}
echo "Servidor de asientos escuchando en el puerto " . PUERTO . "\n";

$clientes = [];
$ocupados = [];
$asientoDe = [];
for ($i = 1; $i <= ASIENTOS; $i++) { //true para asiento ocupado, false para libre
    $ocupados[$i] = false;
}

//enviar mensaje a todos menos al que hizo la operacion
function avisar($clientes, $socket, $mensaje)
{
    foreach ($clientes as $otro) {
        if ($otro !== $socket) {
            fwrite($otro, $mensaje . "\n");
        }
    }
}

while (true) {
    $lectura = $clientes;
    $lectura[] = $servidor;
    $escritura = null;
    $excepciones = null;

    stream_select($lectura, $escritura, $excepciones, null);
    foreach ($lectura as $socket) {
        //si llega un nuevo cliente se agrega a la lista
        if ($socket === $servidor) {
            $nuevo = stream_socket_accept($servidor);
            $clientes[] = $nuevo;
            echo "Se conectó: " . stream_socket_get_name($nuevo, true) . "\n";
            fwrite($nuevo, "Bienvenido a la sala (" . ASIENTOS . " asientos). Comandos: RESERVAR n, LIBERAR, MAPA\n");
            continue;
        }

        $quien = stream_socket_get_name($socket, true);
        $id = (int) $socket;
        $linea = fgets($socket);

        //si el cliente se desconecto se saca de la lista y se libera su asiento
        if ($linea === false) {
            echo "Se desconectó: $quien\n";
            fclose($socket);
            unset($clientes[array_search($socket, $clientes, true)]);
            if (isset($asientoDe[$id])) {
                $n = $asientoDe[$id];
                $ocupados[$n] = false;
                unset($asientoDe[$id]);
                avisar($clientes, $socket, "AVISO asiento $n libre");
            }
            continue;
        }

        $linea = trim(str_replace("\u{FEFF}", '', $linea)); 
        $partes = preg_split('/\s+/', $linea);
        $comando = strtoupper($partes[0]); 
        $cantidad = count($partes);

        if ($comando === 'RESERVAR') {
            $n = $partes[1] ?? '';
            if ($cantidad !== 2 || !ctype_digit($n) || $n < 1 || $n > ASIENTOS) {
                $respuesta = 'ERROR asiento inválido, use 1 a ' . ASIENTOS;
            } elseif (isset($asientoDe[$id])) {
                $respuesta = 'ERROR usted ya tiene el asiento ' . $asientoDe[$id];
            } elseif ($ocupados[(int) $n]) {
                $respuesta = "ERROR el asiento $n ya está ocupado";
            } else {
                $n = (int) $n;
                $ocupados[$n] = true;
                $asientoDe[$id] = $n;
                $respuesta = "OK asiento $n reservado";
                avisar($clientes, $socket, "AVISO asiento $n ocupado");
            }

        } elseif ($comando === 'LIBERAR' && $cantidad === 1) {
            if (!isset($asientoDe[$id])) {
                $respuesta = 'ERROR no tiene asiento reservado';
            } else {
                $n = $asientoDe[$id];
                $ocupados[$n] = false;
                unset($asientoDe[$id]);
                $respuesta = "OK asiento $n liberado";
                avisar($clientes, $socket, "AVISO asiento $n libre");
            }

        } elseif ($comando === 'MAPA' && $cantidad === 1) {
            $mapa = [];
            foreach ($ocupados as $numero => $estaOcupado) {
                $mapa[] = $numero . ':' . ($estaOcupado ? 'O' : 'L');
            }
            $respuesta = 'MAPA ' . implode(' ', $mapa);

        } else {
            $respuesta = 'ERROR comando desconocido';
        }

        echo "[$quien] $linea -> $respuesta\n";
        fwrite($socket, $respuesta . "\n");
    }
}
