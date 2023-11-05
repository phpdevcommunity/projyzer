<?php

namespace App\Helper;

final class Text
{
    public static function strtoupper(?string $value): ?string
    {
        return is_string($value) ? mb_strtoupper($value) : $value;
    }

    public static function strtolower(?string $value): ?string
    {
        return is_string($value) ? mb_strtolower($value) : $value;
    }
}
