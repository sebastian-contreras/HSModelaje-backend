<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $titulo ?? 'Informe' }}</title>
    <style>
        h4 {
            margin-bottom: 0.5rem;
        }

        .w-full {
            width: 100%;
        }

        .w-half {
            width: 50%;
        }

        .w-1-3 {
            width: 33%;
        }

        .margin-top {
            margin-top: 1.25rem;
        }

        .padding-top {
            padding-top: 1rem;
        }

        .footer {
            font-size: 0.875rem;
            padding: 1rem;
            background-color: rgb(241 245 249);
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            font-size: 0.95rem;
        }

        table.report th,
        table.report td {
            border: 1px solid #444;
            padding: 0.6rem;
            text-align: left;
        }

        table.report thead tr:first-child th {
            background-color: #4a90e2;
            color: white;
            text-align: center;
        }

        table.report thead tr:nth-child(2) td {
            background-color: #f0f4f8;
            font-weight: normal;
            font-size: 0.9rem;
        }

        table.report tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        table.report tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }


        .total-row {
            font-weight: bold;
            background-color: rgb(161, 161, 161) !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <table class="w-full">
        <tr>
            <td class="w-1-3">
                @if(isset($image))
                    <img class="padding-top" src="{{ $image }}" alt="Logo" width="150" />
                @endif
            </td>
            <td class="w-half">
                <div>
                    <h4><b>{{ $titulo ?? 'Informe' }}</b></h4>
                </div>
                Fecha de informe: {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    @if(isset($subEncabezado))
        <div class="margin-top">
            <h3><b>{{ $subEncabezado }}</b></h3>
        </div>
    @endif

    <div class="margin-top">
        @foreach ($data as $participante)
            <table class="report">
                <thead>
                    <tr>
                        <th colspan="3" style="background-color: #4a90e2; color: white; font-size: 1.1rem;">
                            {{ $participante['Apelname'] }} (DNI: {{ $participante['DNIModelo'] }})
                        </th>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <strong>Promedio:</strong> {{ $participante['averageScore'] }} &nbsp;&nbsp;|&nbsp;&nbsp;
                            <strong>Total Votos:</strong> {{ $participante['totalVotes'] }} &nbsp;&nbsp;|&nbsp;&nbsp;
                            <strong>Total Métricas:</strong> {{ $participante['totalMetrics'] }}
                        </td>
                    </tr>
                    <tr>
                        <th>Juez</th>
                        <th>Métrica</th>
                        <th>Puntaje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($participante['votes'] as $voto)
                        @foreach ($voto['metrics'] as $metrica)
                            <tr>
                                <td>{{ $voto['judgeName'] }}</td>
                                <td>{{ $metrica['name'] }}</td>
                                <td class="text-center">{{ $metrica['score'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            <br>
        @endforeach
    </div>

    <div class="footer margin-top" style="text-align:center; margin-top: 100px;">
        <script type="text/php">
            if (isset($pdf)) {
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(270, 800, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 10);
            }
        </script>
    </div>
</body>

</html>