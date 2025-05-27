<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BusinessInformationSetting extends Settings
{

    public string $name;

    public ?string $address;

    public ?string $logo;

    public ?string $contact;

    public int $type;

    public static function group(): string
    {
        return 'business_information_settings';
    }
}