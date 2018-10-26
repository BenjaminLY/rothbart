<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <span>Bonjour,</span>
        <p>Merci de votre inscription a notre site {{$data['movie_name']}}</p>
        <p>Pour valider votre inscription, veuillez cliquer sur le lien suivant :</p>
        <a href="{{$data['registration_confirmation_url']}}">Valider son inscription</a>
        <p>A très vite</p>
        <p>Ridley Team</p>
    </body>
</html>
