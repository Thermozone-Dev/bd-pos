<?php

namespace App\Http\Responses;

use App\Filament\Loggers\UserLogger;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->route('filament.admin.pages.dashboard');
    }
}
