<x-mail::message>
# Bonjour

Vous recevez cet email car nous avons reçu une demande de réinitialisation du mot de passe pour votre compte.<br /><br />
Ce code de réinitialisation de mot de passe expirera dans {{ config('auth.passwords.users.expire') }} minutes.
Si vous n\'avez pas demandé de réinitialisation de mot de passe, aucune autre action n\'est requise.

<x-mail::button :url="config('app.front_url').'/password/reset/'.urlencode($user->email)">
Réinitialiser le mot de passe
</x-mail::button>
<br />

Si vous ne parvenez pas à cliquer sur le bouton, copiez et collez l’URL suivant : <a href="{{ config('app.front_url').'/password/reset/'.urlencode($user->email) }}" target="_blank">{{ config('app.front_url').'/password/reset/'.urlencode($user->email) }}</a> dans votre navigateur Web.
<br />
<br />

Cordialement,<br>
**{{ config('app.developer') }}**
</x-mail::message>
