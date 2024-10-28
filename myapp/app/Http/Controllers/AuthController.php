<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Mail\VerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Génération d'un code de vérification
        $verificationCode = Str::random(6);
        $user->verification_code = $verificationCode;

        if ($user->save()) {

            // Envoi de l'email de vérification

            Mail::to($user->email)->send(new VerificationMail($user, $verificationCode));

            //reponse
            return response()->json(['message' => 'Un e-mail de confirmation a été envoyé à votre adresse e-mail. Veuillez suivre les instructions pour confirmer votre compte.'], 200);
        } else {
            return response()->json(['message' => 'Erreur lors de la création de l\'étudiant'], 500);
        }
    }

    public function login(Request $request)
    {
        // validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        $user = User::where('email', '=', $request->email)->first();

        if ($user) {
            // Vérifier si l'email est confirmé
            if (!$user->email_verified_at) {
                return response()->json([
                    'message' => 'Veuillez vérifier votre adresse e-mail avant de vous connecter.'
                ], 403);
            }

    
            if (Hash::check($request->password, $user->password)) {
                // création d'un token JWT
                $accessToken = $user->createToken('authToken')->accessToken;

                return response()->json([
                    'message' => 'Authentification réussie',
                    'accessToken' => $accessToken
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Mot de passe incorrect'
                ], 401);
            }
        } else {
            return response()->json([
                'message' => "L'utilisateur n'existe pas"
            ], 404);
        }
    }

    public function verify($id, $code)
    {
        $user = User::findOrFail($id);

        if ($user->verification_code === $code) {
            $user->email_verified_at = now();
            $user->verification_code = null; // On peut réinitialiser le code après la vérification
            $user->save();

            return response()->json(['message' => 'Email verifie avec succes.']);
        }

        return response()->json(['message' => 'Code de vérification invalide.'], 400);
    }

    public function logout(Request $request)
    {

        Auth::user()->tokens()->delete();

        //destruction du token JWT
        // $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    public function profil(Request $request)
    {

        return response()->json([
            'message' => 'Informations du profil',
            'data' => Auth::user()
        ]);
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $accessToken = $user->createToken('authToken')->accessToken;

        $user->remember_token = Hash::make($accessToken);
        $user->save();

       
        Mail::to($request->email)->send(new ResetPasswordMail($accessToken, $request->email));

        return response()->json([
            'message' => 'Un email de réinitialisation a été envoyé à votre adresse e-mail.',
            'accessToken' => $accessToken
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        // Validation des données reçues
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password_reset_token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password_reset_token, $user->remember_token)) {
            return response()->json(['message' => 'Token de réinitialisation invalide.'], 400);
        }

        // Mise à jour du mot de passe
        $user->password = Hash::make($request->password);
        $user->remember_token = null; // Réinitialisation du token après utilisation
        $user->save();

        return response()->json(['message' => 'Mot de passe réinitialisé avec succès.'], 200);
    }
}
