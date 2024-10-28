<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterMail;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        Subscriber::create([
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'Vous êtes maintenant abonné à la newsletter !'], 201);
    }

    // Méthode pour envoyer la newsletter
    public function send(Request $request)
    {
        // Récupérer tous les abonnés (vous pouvez filtrer les abonnés actifs si nécessaire)
        $subscribers = Subscriber::all();

        foreach ($subscribers as $subscriber) {
            // Envoi de l'e-mail
            Mail::to($subscriber->email)->send(new NewsletterMail($subscriber));
        }

        return response()->json(['message' => 'Newsletter envoyée avec succès !'], 200);
    }
   

    public function unsubscribe($email)
    {
        $subscriber = Subscriber::where('email', $email)->first();
        if ($subscriber) {
            $subscriber->delete();
            return response()->json(['message' => 'Vous avez été désabonné avec succès.']);
        }
        return response()->json(['message' => 'Email non trouvé.'], 404);
    }

}
