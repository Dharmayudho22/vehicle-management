<?php

namespace App\Http\Controllers;

use App\Models\AppLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AppLog::with('user')
            ->when($request->search, function ($query, $search) {
                $query->where('action', 'like', "%{$search}%")
                      ->orWhere('module', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('email', 'like', "%{$search}%")
                            ->orWhere('role', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(50)
            ->withQueryString(); 

        return view('logs.index', compact('logs'));
    }
}