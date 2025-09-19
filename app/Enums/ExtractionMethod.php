<?php

namespace App\Enums;

enum ExtractionMethod: string
{
    case STEAM_DISTILLED = 'steam_distilled';
    case COLD_PRESSED = 'cold_pressed';
    case SOLVENT_EXTRACTION = 'solvent_extraction';
    case CO2 = 'co2';
    case ENFLEURAGE = 'enfleurage';
    case TINCTURE = 'tincture';

    public function label(): string
    {
        return match ($this) {
            self::STEAM_DISTILLED => 'Steam distilled',
            self::COLD_PRESSED => 'Cold pressed',
            self::SOLVENT_EXTRACTION => 'Solvent extraction',
            self::CO2 => 'CO2 extraction',
            self::ENFLEURAGE => 'Enfleurage',
            self::TINCTURE => 'Tincture',
        };
    }
}
