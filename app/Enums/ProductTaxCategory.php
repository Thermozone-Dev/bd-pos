<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProductTaxCategory: string implements HasLabel, HasColor
{
    case ESSENTIAL_MEDICINE = 'essential_medicine';
    case NON_ESSENTIAL_MEDICINE = 'non_essential_medicine';
    case MEDICAL_SUPPLIES = 'medical_supplies';
    case FOOD = 'food';
    case NON_FOOD = 'non_food';
    case ALCOHOL = 'alcohol';
    case TOBACCO = 'tobacco';
    case PERSONAL_CARE = 'personal_care';
    case COSMETICS = 'cosmetics';
    case GROCERY = 'grocery';
    case SERVICE = 'service';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::ESSENTIAL_MEDICINE => 'Essential Medicine',
            self::NON_ESSENTIAL_MEDICINE => 'Non-Essential Medicine',
            self::MEDICAL_SUPPLIES => 'Medical Supplies',
            self::FOOD => 'Food',
            self::NON_FOOD => 'Non-Food',
            self::ALCOHOL => 'Alcohol',
            self::TOBACCO => 'Tobacco',
            self::PERSONAL_CARE => 'Personal Care',
            self::COSMETICS => 'Cosmetics',
            self::GROCERY => 'Grocery',
            self::SERVICE => 'Service',
            self::OTHER => 'Other',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ESSENTIAL_MEDICINE => 'success',
            self::NON_ESSENTIAL_MEDICINE => 'warning',
            self::MEDICAL_SUPPLIES => 'success',
            self::FOOD => 'success',
            self::NON_FOOD => 'warning',
            self::ALCOHOL => 'warning',
            self::TOBACCO => 'warning',
            self::PERSONAL_CARE => 'warning',
            self::COSMETICS => 'warning',
            self::GROCERY => 'success',
            self::SERVICE => 'info',
            self::OTHER => 'info',
        };
    }

    public function isVatExempt(): bool
    {
        return match ($this) {
            self::ESSENTIAL_MEDICINE => true,
            self::NON_ESSENTIAL_MEDICINE => false,
            self::MEDICAL_SUPPLIES => true,
            self::FOOD => true,
            self::NON_FOOD => false,
            self::ALCOHOL => false,
            self::TOBACCO => false,
            self::PERSONAL_CARE => false,
            self::COSMETICS => false,
            self::GROCERY => true,
            self::SERVICE => false, // Add Logic for Nature of Business
            self::OTHER => false,
        };
    }


}
