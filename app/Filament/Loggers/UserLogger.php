<?php

namespace App\Filament\Loggers;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class UserLogger extends Logger
{
    public static ?string $model = User::class;

    public static function getLabel(): string | Htmlable | null
    {
        return UserResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('name')
                    ->label('Name'),
                Field::make('email')
                    ->label('E-Mail'),
                Field::make('time-in')
                    ->label('Time In'),
                Field::make('time-out')
                    ->label('Time Out'),
                // Field::make('roles.name')
                //     ->hasMany('permissions')
                //     ->label(__('Permissions'))
                //     ->badge(),
            ])
            ->relationManagers([
                //
            ]);
    }

    public function login() : Void {
        $attributes = [
            'time-in' => now(),
        ];

        $this->log(
            ["attributes" => $attributes],
            event: "login",
        );
    }

    public function logout() : Void {
        $attributes = [
            'time-out' => now(),
        ];

        $this->log(
            ["attributes" => $attributes],
            event: "login",
        );
    }
}
