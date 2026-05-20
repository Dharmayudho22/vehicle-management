<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SwitchAccountController extends Controller
{
    public function switch(Request $request, User $user)
    {
        // Simpan original admin ID pertama kali
        if (!session()->has('original_user_id')) {
            session(['original_user_id' => auth()->id()]);
        }

        auth()->login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Beralih ke akun ' . $user->name);
    }
}