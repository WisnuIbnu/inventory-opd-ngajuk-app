<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements Responsable
{
    /**
     * Mengalihkan pengguna ke halaman root (/) setelah logout.
     */
    public function toResponse($request): RedirectResponse
    {
        return redirect('/');
    }
}