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

        table {
            width: 100%;
            border-spacing: 0;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        table.report th,
        table.report td {
            border: 1px solid #000;
            padding: 0.5rem;
            text-align: left;
        }

        table.report th {
            background-color: rgb(96, 165, 250);
            color: rgb(0, 0, 0);
        }

        table.report td {
            background-color: rgb(241, 245, 249);
        }

        table.report tr:nth-child(even) td {
            background-color: rgb(220, 230, 240);
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
        @foreach($tablas as $tabla)
            <div class="margin-top">
                <h3><b>{{ $tabla['titulo'] }}</b></h3>
                <table class="report">
                    <thead>
                        <tr>
                            @foreach($tabla['cabecera'] as $columna)
                                <th>{{ $columna }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tabla['data'] as $item)
                            <tr @if(isset($item->isTotal) && $item->isTotal) class="total-row" @endif>
                                @foreach($tabla['cabecera'] as $key => $columna)
                                                            @php
                                                                $value = '';
                                                                foreach ($item as $prop => $val) {
                                                                    if (
                                                                        strtolower($prop) == strtolower($key) ||
                                                                        strtolower($prop) == strtolower(str_replace(' ', '_', $key))
                                                                    ) {
                                                                        $value = $val;
                                                                        break;
                                                                    }
                                                                }
                                                            @endphp
                                    <td class="{{ is_numeric($value) ? 'text-right' : '' }}">
                                                                @if(is_numeric($value))
                                                                    {{ number_format($value, 2, ',', '.') }}
                                                                @else
                                                                    {{ $value ?? '' }}
                                                                @endif
                                                            </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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