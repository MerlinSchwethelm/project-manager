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

    /**
     * @return string[]
     */
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

    /**
     * @return array<string, string>
     */
    public static function getKeyValuePairs(): array
    {
        $items = self::cases();
        $keyValuePairs = [];

        foreach ($items as $item) {
            $keyValuePairs[$item->value] = ucwords(str_replace('_', ' ', $item->value));
        }

        return $keyValuePairs;
    }
}
