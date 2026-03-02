<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1"

    </head>
    <body>
    <header>
        <a href="{{ route('devis.index') }}" class="btn-new">Devis</a><br>
        <a href="{{ route('tarifs.tarifs') }}" class="btn-new">Tarifs</a>
    </header>

    <footer>

    </footer>
    </body>
</html>
