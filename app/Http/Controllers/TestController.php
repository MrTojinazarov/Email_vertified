<?php

namespace App\Http\Controllers;

use App\Mail\SendMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{

    public function index()
    {
        $users = User::all();
        return view('email-test', ['users' => $users]);
    }

    public function create(Request $request, User $user)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'text' => 'required'
        ]);
        $token = '';
    
        $response = Http::withToken($token)->post('https://notify.eskiz.uz/api/message/sms/send', $data);

        return back();
    }
    public function showVerificationPage()
    {
        return view('accept-code');
    }


    public function verifyCode(Request $request)
    {
        $request->validate(['verification_code' => 'required']);
    
        $user = Auth::user();
        
        if($user->verification_code == $request->verification_code){
            $user->is_verified = true;
            $user->verification_code = null;
            $user->email_verified_at = Carbon::now();
            $user->save();
            
            return redirect()->route('dashboard')->with('success', 'Email muvaffaqiyatli tasdiqlandi!');
        } else {
            return back()->withErrors(['verification_code' => 'Kod noto\'g\'ri, qayta kiriting.']);
        }
    }
    
}
