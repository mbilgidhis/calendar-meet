<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Socialite;
use Auth;
use Exception;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class GoogleController extends Controller
{
    public function redirectToGoogle(){
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback (Request $request) {
        try {
            $user = Socialite::driver('google')->user();
            // dd($user);
            $findUserEmail = User::where('email', $user->email)->first();
            // dd($findUser);
            if ($findUserEmail) {
                if( !$findUserEmail->google_id ) {
                    $findUserEmail->google_id = $user->id;
                    $findUserEmail->save();
                }
                Auth::login($findUserEmail);
                return redirect('/home');
            } else {
                $findUserId = User::where('google_id', $user->id)->first();
                // dd($findUserId);
                if ( Route::has('register') ) {
                    $newUser = User::create([
                        'id' => Str::uuid(),
                        'email' => $user->email,
                        'name' => $user->name,
                        'google_id' => $user->id,
                        'password' => Hash::make('password123'),
                    ]);
    
                    Auth::login($newUser);
                    return redirect('/home');
                } else {
                    abort(404);
                }
            }
        } catch (Exception $e) {
            abort(404);
        }
    }
}
