<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('business_information_settings.name', config('app.name'));
        $this->migrator->add('business_information_settings.address', null);
        $this->migrator->add('business_information_settings.logo', null);
        $this->migrator->add('business_information_settings.contact', false);
        $this->migrator->add('business_information_settings.type', 0);
    }
};
