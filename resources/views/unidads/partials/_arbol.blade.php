{{-- Partial recursivo: renderiza un nivel del árbol de unidades.
     Espera: $nodos (colección de unidades del nivel actual) y
             $hijosPorPadre (colección agrupada por padre_id). --}}
<ul class="tree-unidades">
    @foreach ($nodos as $nodo)
        @php $hijos = $hijosPorPadre[$nodo->id] ?? collect(); @endphp
        <li class="{{ $hijos->count() ? 'tiene-hijos' : '' }}">
            <span class="nodo">
                @if ($hijos->count())
                    <i class="fa fa-caret-down toggle" aria-hidden="true"></i>
                @else
                    <i class="fa fa-circle-o hoja" aria-hidden="true"></i>
                @endif
                <a href="{{ route('unidads.show', $nodo->id) }}">{{ $nodo->nombre }}</a>
                @if ($nodo->sigla)<small class="text-muted">({{ $nodo->sigla }})</small>@endif
                @if (! $nodo->activa)<span class="label label-default">inactiva</span>@endif
            </span>
            @if ($hijos->count())
                @include('unidads.partials._arbol', ['nodos' => $hijos, 'hijosPorPadre' => $hijosPorPadre])
            @endif
        </li>
    @endforeach
</ul>
