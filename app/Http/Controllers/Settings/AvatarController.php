<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = $request->user();
        $old = $user->getRawOriginal('avatar');

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->avatar = $path;
        $user->save();

        if ($old) {
            Storage::disk('public')->delete($old);
        }

        return back();
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $old = $user->getRawOriginal('avatar');

        $user->avatar = null;
        $user->save();

        if ($old) {
            Storage::disk('public')->delete($old);
        }

        return back();
    }
}
