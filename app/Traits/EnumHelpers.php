<?php

namespace App\Traits;

trait EnumHelpers
{
    /**
     * @return string[]
     */
    public static function values(): array
    {
        $items = self::cases();
        $values = [];

        foreach ($items as $item) {
            $values[] = $item->value;
        }

        return $values;
    }

    public static function labels(): array
    {
        $items = self::cases();
        $labels = [];

        foreach ($items as $item) {
            $label = str_replace('_', ' ', $item->value);
            $labels[] = ucwords($label);
        }

        return $labels;
    }
}
