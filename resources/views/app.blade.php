<!DOCTYPE html>
<html class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <script src="{{ mix('/js/app.js') }}" defer></script>
    @inertiaHead
</head>
<body class="font-sans antialiased leading-none text-gray-700">
    @inertia
</body>
</html>
