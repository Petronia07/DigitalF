
<p>Bonjour {{ $user->name }},</p>

<p>Veuillez vérifier votre adresse e-mail en cliquant sur le lien ci-dessous :</p>

<a href="{{ url('/api/email/verification', ['id' => $user->id, 'code' => $verificationCode]) }}">Vérifier mon e-mail</a>

<p>Merci,</p>
<p>L'équipe {{ config('app.name') }}</p>