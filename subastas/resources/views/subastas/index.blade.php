<h1>Subastas</h1>


@if (session('error'))
    <p style="color: red">{{ session('error') }}</p>
@endif


@foreach ($subastas as $subasta)
    <div>
        <h3>{{ $subasta->titulo }}</h3>
        <p>{{ $subasta->descripcion }}</p>
        <p>Precio actual: $<span id="precio-{{ $subasta->id }}">{{ $subasta->precio_actual }}</span></p>


        <form method="POST" action="/subastas/{{ $subasta->id }}/pujar">
            @csrf
            <input type="number" name="monto" required>
            <button>Pujar</button>
        </form>
    </div>
@endforeach
