<?php
$servidor = stream_socket_server('tcp://0.0.0.0:9000', $codigo, $error);
if (!$servidor) {
    die("no se pudo iniciar el servidor: $error\n");
}
echo "calculadora escuchando en el puerto 9000\n";

$ayuda = 'OK comandos: SUMA a b, RESTA a b, MULT a b, DIV a b, AYUDA, SALIR';

//revisa que lleguen 2 numeros validos y hace la operacion
function calcular($comando, $partes)
{
    if (count($partes) !== 3) {
        return "ERROR cantidad incorrecta de numeros, use: $comando a b";
    }
    $a = $partes[1];
    $b = $partes[2];

    if (!is_numeric($a) || !is_finite((float) $a)) {
        return "ERROR '$a' no es un numero";
    }
    if (!is_numeric($b) || !is_finite((float) $b)) {
        return "ERROR '$b' no es un numero";
    }
    $a = (float) $a;
    $b = (float) $b;

    switch ($comando) {
        case 'SUMA':
            $resultado = $a + $b;
            break;
        case 'RESTA':
            $resultado = $a - $b;
            break;
        case 'MULT':
            $resultado = $a * $b;
            break;
        case 'DIV':
            if ($b == 0) {
                return 'ERROR no se puede dividir para cero';
            }
            $resultado = $a / $b;
            break;
    }
    return 'OK ' . $resultado;
}

while (true) {
    $cliente = stream_socket_accept($servidor, -1, $quien);
    if (!$cliente) {
        continue;
    }
    echo "Se conecto: $quien\n";
    fwrite($cliente, "OK Bienvenido a la calculadora remota, escriba AYUDA para ver los comandos\n");

    //leer operaciones del cliente
    while (($linea = fgets($cliente)) !== false) {
        $linea = trim(str_replace("\u{FEFF}", '', $linea)); // quita caracter invisible que envia PowerShell
        $partes = preg_split('/\s+/', $linea); //separar por espacios
        $comando = strtoupper($partes[0]); //para aceptar suma o SUMA
        $cantidad = count($partes);

        if ($comando === '') {
            $respuesta = 'ERROR comando vacio, escriba AYUDA';

        } elseif ($comando === 'SUMA' || $comando === 'RESTA' || $comando === 'MULT' || $comando === 'DIV') {
            $respuesta = calcular($comando, $partes);

        } elseif (($comando === 'AYUDA' || $comando === 'SALIR') && $cantidad > 1) {
            $respuesta = "ERROR cantidad incorrecta de numeros, $comando no lleva numeros";

        } elseif ($comando === 'AYUDA') {
            $respuesta = $ayuda;

        } elseif ($comando === 'SALIR') {
            $respuesta = 'OK adios';

        } else {
            $respuesta = "ERROR comando desconocido '$partes[0]', escriba AYUDA";
        }

        echo "[$quien] $linea -> $respuesta\n";
        fwrite($cliente, $respuesta . "\n");

        if ($respuesta === 'OK adios') {
            break;
        }
    }

    fclose($cliente);
    echo "Se desconecto: $quien\n";
}
