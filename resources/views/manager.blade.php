<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('manager::partials.head', [
    'scripts' => ['resources/css/app.css', 'resources/js/app.js']
])
<body>
<div id="app"></div>
</body>
</html>
