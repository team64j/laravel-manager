<head>
    <base href="{{ url('/' . config('cms.mgr_dir')) }}/">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset($basePath . 'resources/js/assets/logo.svg') }}" type="image/svg+xml">
    <title>{{ config('global.site_name') }} ({{ config('app.name') }})</title>
    @vite($scripts)

</head>
