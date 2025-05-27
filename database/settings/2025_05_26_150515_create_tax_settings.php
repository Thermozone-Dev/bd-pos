<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('tax_settings.vat_adjustments', false);
        $this->migrator->add('tax_settings.vat_deduction', false);
        $this->migrator->add('tax_settings.amusement_tax', true);
        $this->migrator->add('tax_settings.is_inclusive', true);

    }
};
