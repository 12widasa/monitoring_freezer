<?php

use Carbon\CarbonImmutable;

if (! function_exists('local_datetime')) {
    function local_datetime(
        ?\DateTimeInterface $value,
        string $format = 'd/m/Y, H.i',
        string $fallback = 'Tidak tersedia',
        bool $translated = false,
    ): string {
        if ($value === null) {
            return $fallback;
        }

        $localValue = CarbonImmutable::instance($value)
            ->setTimezone(
                config(
                    'app.display_timezone',
                    'Asia/Jakarta',
                ),
            );

        if ($translated) {
            return $localValue
                ->locale(config('app.locale', 'en'))
                ->translatedFormat($format);
        }

        return $localValue->format($format);
    }
}
