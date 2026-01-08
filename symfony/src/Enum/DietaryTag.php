<?php

namespace App\Enum;

enum DietaryTag: string {
    case VEGETARIAN = 'vegeterian';
    case VEGAN = 'vegan';
    case GLUTEN_FREE = 'gluten_free';
    case LACTOSE_FREE = 'lactose_free';
    case KETO = 'keto';
    case LOW_CARB = 'low_carb';
    case PESCATARIAN = 'pescatarian';

    public function getLabel():string {
        return match($this) {
            self::VEGETARIAN => 'Wegetariańskie',
            self::VEGAN => 'Wegańskie',
            self::GLUTEN_FREE => 'Bez glutenu',
            self::LACTOSE_FREE => 'Bez laktozy',
            self::KETO => 'Keto',
            self::LOW_CARB => 'Niskowęglowodanowe',
            self::PESCATARIAN => 'Peskatariańskie'
        };
    }
}
