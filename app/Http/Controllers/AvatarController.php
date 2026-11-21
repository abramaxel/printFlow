<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function show(User $user)
    {
        $path = (string) ($user->avatar ?? '');

        abort_if($path === '', 404);
        abort_if(str_contains($path, '..'), 404);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path), [
            'Content-Type'           => Storage::disk('public')->mimeType($path) ?: 'image/jpeg',
            'Cache-Control'          => 'private, max-age=604800',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}