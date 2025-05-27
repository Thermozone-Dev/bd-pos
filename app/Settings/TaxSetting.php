<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class TaxSetting extends Settings
{

    public bool $vat_adjustments;

    public bool $vat_deduction;

    public bool $amusement_tax;

    public bool $is_inclusive;

    public static function group(): string
    {
        return 'tax_settings';
    }

}