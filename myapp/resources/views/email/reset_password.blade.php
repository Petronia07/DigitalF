<p>Bonjour,</p>

<p>Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>

<a href="{{ url('password.reset', ['email' => $email, 'accessToken' => $accessToken]) }}">
    Réinitialiser mon mot de passe
</a>

<p>Si vous n'avez pas demandé cette réinitialisation, ignorez cet e-mail.</p>

<p>Merci,</p>
<p>L'équipe {{ config('app.name') }}</p>