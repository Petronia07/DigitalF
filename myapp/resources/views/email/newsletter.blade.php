@component('mail::message')
# Bonjour {{ $email }},

Nous sommes ravis de vous envoyer notre dernière newsletter. Voici quelques nouveautés et conseils intéressants !

## Ce que vous trouverez dans cette édition :

- **Dernières actualités**
- **Astuces et tutoriels**
- **Offres exclusives**
  
@component('mail::button', ['url' => 'https://dflgroupe.com'])
Visitez notre site
@endcomponent

Nous vous remercions de faire partie de notre communauté !

Cordialement,  
L'équipe DFL Groupe.

---

Si vous ne souhaitez plus recevoir nos emails, vous pouvez [vous désabonner](https://dflgroupe.com/unsubscribe).
@endcomponent
<a href="{{ url('unsubscribe', $subscriber->email) }}">Se désabonner</a>
