<?php

namespace App\Enums;

enum MenuCategory: string
{
    case Coffee = 'coffee';
    case NonCoffee = 'non_coffee';
    case HeavyMeal = 'heavy_meal';
    case Snack = 'snack';

    public function label(): string
    {
        return match ($this) {
            self::Coffee => 'Kopi',
            self::NonCoffee => 'Non-Kopi',
            self::HeavyMeal => 'Makanan Berat',
            self::Snack => 'Camilan',
        };
    }

    /**
     * @return array<string, string> value => label
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
