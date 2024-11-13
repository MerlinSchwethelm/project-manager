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
}
