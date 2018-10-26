<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <span>Bonjour,</span>
        <p>Merci de votre achat pour télécharger le film {{$data['movie_name']}}</p>
        <p>Connectez-vous avec vos identifiants pour le visualiser. Vous disposez de 7 jours pour le visionner en ligne.</p>
        <a href="{{$data['home_url']}}">Voir le film</a>
        <p>A très vite</p>
        <p>Ridley Team</p>
    </body>
</html>
