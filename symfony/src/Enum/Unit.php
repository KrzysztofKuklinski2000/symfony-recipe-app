<?php
namespace App\Enum;


enum Unit: string {
    case GRAM = 'g';
    case KILOGRAM = 'kg';
    case MILLILITER = 'ml';
    case LITRE = 'l';


    public function getLabel(): string {
        return match($this) {
            self::GRAM => 'Gram (g)',
            self::KILOGRAM => 'Kilogram (kg)',
            self::MILLILITER => 'Mililitr (ml)',
            self::LITRE => 'Litr (l)'
        };
    }
}
