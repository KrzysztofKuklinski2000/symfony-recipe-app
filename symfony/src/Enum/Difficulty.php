<?php

namespace App\Enum;

enum Difficulty: string {
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';

    public function getLabel():string {
        return match($this) {
            self::EASY => 'Łatwy',
            self::MEDIUM => 'Średni',
            self::HARD => 'Trudny'
        };
    }
}
