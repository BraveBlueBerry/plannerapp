<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" />
</head>
<body>
    <div id="app">
        <app></app>
    </div>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>

<!--
Planning tool bestaande uit een api en een frontend.

De kenmerken hiervoor zijn:
* Een lijst met items tonen;
* Een item kunnen bewerken, denk aan de velden datum, titel omschrijving, etc;
* Een bijlage toevoegen aan een item;
* De postdata valideren zowel backend/frontend;
* Een item kunnen verwijderen van de lijst;

Voor de backend:
Toepassing van het Laravel Framework voor het opslaan van de data.
Database maakt niet uit, standaard zou je zelfs sqlite kunnen gebruiken, aan de code kan ik wel zien of die opslaat of niet.
Documentatie is hier te vinden:
https://laravel.com/docs

Voor video uitleg van heel veel disciplines voor Laravel/PHP/Javascript is:
http://laracasts.com

Voor de FrontEnd:
Het toepassen van een framework die MVVM kan ondersteunen.
Dat houd eigenlijk in dat het een Single Page Application is waarbij de lijst beheert kan worden zonder herladen van de pagina.

Graag een van de volgende frameworks toepassen voor de frontend
* Angular >= 2 /Ionic >= 2
* VueJS
* React
-->
