<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'module', 'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, string $module, array $payload = []): void
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->guard('web')->user();

        static::create([
            'user_id' => $user?->id,
            'action' => $action,
            'module' => $module,
            'payload' => $payload ?: null,
        ]);
    }
}
