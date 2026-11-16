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
            ['Laptop Lenovo ThinkPad', 'Procesador i5, 16GB RAM, bateria dura 3 horas', 450],
            ['Bicicleta de montaña', 'Aro 29, frenos de disco, poco uso', 280],
            ['Televisor LG 55"', 'Smart TV 4K, incluye control remoto', 380],
            ['Consola PlayStation 5', 'Con dos controles y tres juegos', 520],
            ['Refrigeradora Indurama', 'Dos puertas, color gris, funciona perfecto', 600],
            ['Guitarra acustica Yamaha', 'Tiene una pequeña raspadura en la parte trasera', 150],
            ['Reloj Casio G-Shock', 'Resistente al agua, correa nueva', 90],
            ['Camara Canon EOS', 'Incluye lente 18-55mm y estuche', 700],
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
