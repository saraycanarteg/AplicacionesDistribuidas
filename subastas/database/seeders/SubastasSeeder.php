<?php

namespace Database\Seeders;
use App\Models\Subasta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubastasSeeder extends Seeder
{
    public function run(): void
    {
        $articos = [
            ['Carro Mazda', 'Tiene un rayon dentro de la puerta derecha',5000],
            ['Celular Smsung A56', 'Es de color blanco y la pantalla esta trizada', 100], 
        ];

        foreach($articos as [ $titulo, $descripcion, $precio]){
            Subasta::create([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'precio_inicial' => $precio,
                'precio_actual' => $precio,
                'termina_en' => now() -> addDays(2),
            ]);
        }
    
    }
}
