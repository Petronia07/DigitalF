<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);
        //dd($request->all());
    
        // Envoi de l'email
        Mail::to('taty@gmail.com')->send(new ContactMail($request->only(['name', 'email', 'message'])));
    
        return response()->json(['message' => 'Votre message a été envoyé avec succès !'], 200);
    }
    
}
