<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SubastaController;

Route::get('/', function () {
    //return view('welcome');
    $nodo = gethostname();
    try{
        //enviamos una consulta al nodo db por la red
        $version = DB::scalar('select version()');
        $estadoDb = 'Funcionando: '.$version;
    }catch (Throwable $e){
        //db no respondio: no rompemos la pagina solo informamos
        $estadoDb = 'Caído: '. $e-> getMessage();
    }
    return "Servidor web (app): Funcionando en el nodo $nodo <br><br>"
            . "Base de datos (db): $estado";    

});

Route::get('/subastas', [SubastaController::class,'index']);
Route::post('/subastas/{subasta}/pujar', [SubastaController::class, 'pujar']);

Route::get('/sala', function () {
    return view('sala');
});
