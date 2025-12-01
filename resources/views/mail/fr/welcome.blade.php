<x-mail::message>
# Bonjour

Vous recevez cet email car vous êtes actuellement inscrit sur **{{ config('app.name') }}**<br /><br />
Voici les identifiants de votre compte que vous ne devez communiquer à personne.

<x-mail::panel>
## Adresse électronique : {{ $user->email }} <br />
## Mot de passe : {{ $pass }} <br />
</x-mail::panel>

<x-mail::button :url="config('app.front_url').'/login'">
Connexion
</x-mail::button>
<br />

Si vous ne parvenez pas à cliquer sur le bouton, copiez et collez l’URL suivant : <a href="{{ config('app.front_url').'/auth/login' }}" target="_blank">{{ config('app.front_url').'/auth/login' }}</a> dans votre navigateur Web.
<br />
<br />
Cordialement,<br>
**{{ config('app.developer') }}**
</x-mail::message>
