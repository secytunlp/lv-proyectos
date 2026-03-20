<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        h3 {
            margin: 10px 0 5px 0;
        }

        .header {
            border-bottom: 2px solid #000;
            margin-bottom: 5px;
            padding-bottom: 5px;
        }

        .linea {
            border-bottom: 1px solid #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            page-break-inside: avoid;
        }

        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: left;
        }

        th {
            background-color: #eaeaea;
        }

        tr {
            page-break-inside: avoid;
        }

        .page-break {
            page-break-before: always;
        }

        .small {
            font-size: 12px;
        }
        .proyecto {
            margin-bottom: 12px; /* 👈 espacio entre proyectos */
            page-break-inside: avoid; /* 👈 clave */
        }
    </style>
</head>
<body>

@foreach($proyectos as $facultad => $items)

    @if(!$loop->first)
        <div class="page-break"></div>
    @endif

    <div class="header">
        <h2>Acreditación {{ $year }} (Importados desde SIGEVA)</h2>
    </div>

    <h3>
        {{ $items->first()->facultad_nombre }}
    </h3>

    @foreach($items as $proyecto)
        <div class="proyecto">
        <div class="small">
            <strong>Proyecto:</strong> {{ $proyecto->codigo ?? '' }} |
            <strong>Tipo:</strong> {{ $proyecto->tipo ?? '' }} |
            <strong>Inicio:</strong> {{ \Carbon\Carbon::parse($proyecto->inicio)->format('d/m/Y') }} |
            <strong>Fin:</strong> {{ \Carbon\Carbon::parse($proyecto->fin)->format('d/m/Y') }}
        </div>

        <div class="small">
            <strong>Título:</strong> {{ $proyecto->titulo ?? '' }}
        </div>
            <div class="small">
                <strong>Unidad:</strong> {{ optional($proyecto->unidad)->nombre }} - {{ optional($proyecto->unidad)->sigla }}
            </div>



        <table>
            <thead>
            <tr>
                <th>Integrante</th>
                <th>Tipo</th>
                <th>SPU / SICADI</th>
                <th>Cargo</th>
                <th>Beca</th>
                <th>Carrera Inv.</th>
                <th>Hs</th>
            </tr>
            </thead>
            <tbody>
            @foreach($proyecto->integrantes as $i)
                <tr>
                    <td>
                        {{ optional(optional($i->investigador)->persona)->full_name }}
                    </td>

                    {{-- Tipo abreviado --}}
                    <td>
                        @switch($i->tipo)
                            @case('Director') D @break
                            @case('Codirector') CD @break
                            @case('Investigador Formado') IF @break
                            @case('Investigador En Formación') IEF @break
                            @case('Investigador En FormaciÃ³n') IEF @break
                            @case('Becario, Tesista') B @break
                            @case('Colaborador') C @break
                            @default -
                        @endswitch
                    </td>

                    {{-- Categoria / Sicadi --}}
                    <td>
                        {{ collect([
                            optional($i->categoria)->nombre,
                            optional($i->sicadi)->nombre
                        ])->filter()->implode(' / ') }}
                    </td>


                    <td>
                        {{ collect([
                            optional($i->cargo)->nombre,
                            $i->deddoc
                        ])->filter()->implode(' / ') }}
                    </td>
                    <td>
                        {{ collect([
                            $i->beca,
                            $i->institucion
                        ])->filter()->implode(' / ') }}
                    </td>

                    <td>
                        {{ collect([
                            optional($i->carrerainv)->nombre,
                            $i->organismo_codigo
                        ])->filter()->implode(' / ') }}
                    </td>



                    <td>{{ $i->horas }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    @endforeach

@endforeach

</body>
</html>
