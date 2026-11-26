<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/js/app.js'])
    @inertiaHead
    @foreach (data_get($page, 'props.seo.jsonLd', []) as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endforeach
</head>
<body class="font-sans antialiased text-ink bg-white">
    @inertia
</body>
</html>
