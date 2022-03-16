<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}{{ ($begin ? '_от_'.$begin : '') }}{{ ($end ? '_до_'.$end : '') }}{{ '_'.now()->format('H.i.d.m.Y')  }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            border: 0px;
        }

        body {
            background-color: #FFF;
            font-size: 14px;
            text-align: left;
            font-family: Tahoma, Arial, Verdana, Times New Roman, sans-serif;
        }

        #container {
            width: 960px;
            margin: 30px auto;
            text-align: left;
        }

        h1 {
            font-size: 18px;
            font-weight: normal;
            text-align: center;
        }

        h2 {
            font-size: 16px;
            font-weight: normal;
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table th,
        td {
            border: 1px solid #000;
            text-align: left;
            font-size: 14px;
            padding: 7px;
        }

        .text-end {
            text-align: right;
            padding-top: 5px;
        }

    </style>
</head>

<body>
    <div id="container">
        <div style="display: flex; align-items: center;padding-bottom: 10px">
            <h3>
                {{ $title }}
            </h3>
            @if ($begin)
                <p style="padding-left: 5px">
                     от:{{ $begin }}
                </p>
            @endif
            @if ($end)
                <p style="padding-left: 5px">
                     до:{{ $end }}
                </p>
            @endif
        </div>
        {!! $report !!}
        <div class="text-end">
            {{ now()->format('H:i d.m.Y') }}
        </div>
    </div>
</body>

</html>
