<?php
$saldo = 100;

$servidor = stream_socket_server('tcp://0.0.0.0:9000', $codigo, $error);
if (!$servidor) {
    die("no se pudo iniciar el servidor: $error\n");
}
echo "cajero escuchando en el puerto 9000\n";


function validar($monto)
{
    if ($monto === null || $monto === '') {
        return 'ERROR falta el monto';
    }
    if (!is_numeric($monto) || !is_finite((float) $monto)) {
        return 'ERROR el monto no es un numero';
    }
    if ($monto <= 0) {
        return 'ERROR el monto debe ser mayor que cero';
    }
    if (preg_match('/\.\d{3,}$/', $monto)) {
        return 'ERROR el monto solo puede tener hasta 2 decimales';
    }
    return round((float) $monto, 2);
}

//muestra valores en formatos enteros o con 2 deciamles
function formatear($numero)
{
    return rtrim(rtrim(number_format($numero, 2, '.', ''), '0'), '.');
}

while (true) {
    $cliente = stream_socket_accept($servidor, -1, $quien); 
    if (!$cliente) {
        continue;
    }
    echo "Se conecto: $quien\n";
    fwrite($cliente, "Bienvenido al cajero. Comandos: SALDO, DEPOSITAR n, RETIRAR n, SALIR\n");
    
    //leer instrucciones del cliente
    while (($linea = fgets($cliente)) !== false) {
        $linea = trim(str_replace("\u{FEFF}", '', $linea)); // quita caracter invisible que envia PowerShell
        $partes = preg_split('/\s+/', $linea); //dividir entrada por espacios en blanco
        $comando = strtoupper($partes[0]); //convertir a mayusculas
        $monto = $partes[1] ?? null; //obtener valor ingresado
        $cantidad = count($partes); 

        if (($comando === 'SALDO' || $comando === 'SALIR') && $cantidad > 1) {
            $respuesta = "ERROR opcion no disponible, $comando no lleva datos extra";
        } elseif (($comando === 'DEPOSITAR' || $comando === 'RETIRAR') && $cantidad > 2) {
            $respuesta = "ERROR opcion no disponible, use: $comando monto";

        } elseif ($comando === 'SALDO') {
            $respuesta = 'OK saldo ' . formatear($saldo);

        } elseif ($comando === 'DEPOSITAR') {
            $valor = validar($monto);
            if (is_string($valor)) {
                $respuesta = $valor;
            } else {
                $saldo = round($saldo + $valor, 2);
                $respuesta = 'OK nuevo saldo ' . formatear($saldo);
            }

        } elseif ($comando === 'RETIRAR') {
            $valor = validar($monto);
            if (is_string($valor)) {
                $respuesta = $valor;
            } elseif ($valor > $saldo) {
                $respuesta = 'ERROR saldo insuficiente, saldo actual ' . formatear($saldo);
            } else {
                $saldo = round($saldo - $valor, 2);
                $respuesta = 'OK nuevo saldo ' . formatear($saldo);
            }

        } elseif ($comando === 'SALIR') {
            $respuesta = 'ADIOS';

        } elseif ($comando === '') {
            $respuesta = 'ERROR comando vacio';

        } else {
            $respuesta = 'ERROR opcion no disponible';
        }

        echo "[$quien] $linea -> $respuesta\n";
        fwrite($cliente, $respuesta . "\n");

        if ($respuesta === 'ADIOS') {
            break;
        }
    }

    fclose($cliente);
    echo "Se desconecto: $quien\n";
}
