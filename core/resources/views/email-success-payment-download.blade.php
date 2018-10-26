<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <span>Bonjour,</span>
        <p>Merci de votre achat pour télécharger le film {{$data['movie_name']}}</p>
        <p>Cliquez sur le lien ci-dessous pour le télécharger - Vous disposez de 48h pour le télécharger et de 7 jours pour le visionner en ligne.</p>
        <a href="{{$data['home_url']}}">Télécharger le film</a>
        <p>A très vite</p>
        <p>Ridley Team</p>
    </body>
</html>
