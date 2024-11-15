<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendMessage;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^\+998\d{9}$/', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);
    
        event(new Registered($user));
        Auth::login($user);
    
        $code = rand(11111, 99999);
    
        $data = [
            'mobile_phone' => $user->phone,
            'message' => 'Bu Eskiz dan test',
            'from' => '4546',
            'callback_url' => route('dashboard'), 
        ];
    
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3MzQyNzI0MjIsImlhdCI6MTczMTY4MDQyMiwicm9sZSI6InRlc3QiLCJzaWduIjoiNmFkMTUwMjJmYTcwNTM0OTU1MGE1NzQ5MWFiNDQ5ZjI2NGVhMWNmZWQ5YTgzYWNhZjM1MGVlZjdhMmE2MDhjMyIsInN1YiI6Ijg5MjUifQ.lyHF92FXrI7yAH2wGYNq6qfAZT8klg9NhRHk0GUvvFs';
    
        $response = Http::withToken($token)->post('https://notify.eskiz.uz/api/message/sms/send', $data);
    
        if ($response->successful()) {
            $user->verification_code = $code;
            $user->save();
            return redirect(route('verification.page'));
        }
    
        return redirect()->back()->withErrors(['sms' => 'SMS yuborishda xatolik yuz berdi.']);
    }
    
    
}
