<?php

namespace App\Tool;

class TextProcessor
{
    public static function trimData(array $data): array
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return self::trimData($value);
            }
            if (is_string($value)) {
                return trim($value);
            }
            return $value;
        }, $data);
    }
}

