<?php

namespace App\Enums\Concerns;

trait HasOptions
{
    /** @return array<string, string> nilai => label, untuk dropdown */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
