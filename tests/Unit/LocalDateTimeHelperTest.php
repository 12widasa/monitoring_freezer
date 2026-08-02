<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class LocalDateTimeHelperTest extends TestCase
{
    public function test_it_converts_utc_datetime_to_display_timezone(): void
    {
        config([
            'app.display_timezone' => 'Asia/Jakarta',
        ]);

        $value = CarbonImmutable::create(
            year: 2026,
            month: 8,
            day: 2,
            hour: 0,
            minute: 35,
            second: 0,
            timezone: 'UTC',
        );

        $this->assertSame(
            '02/08/2026, 07.35',
            local_datetime($value),
        );
    }

    public function test_it_uses_the_configured_display_timezone(): void
    {
        config([
            'app.display_timezone' => 'Asia/Makassar',
        ]);

        $value = CarbonImmutable::create(
            year: 2026,
            month: 8,
            day: 2,
            hour: 0,
            minute: 35,
            second: 0,
            timezone: 'UTC',
        );

        $this->assertSame(
            '02/08/2026, 08.35',
            local_datetime($value),
        );
    }

    public function test_it_supports_custom_format_and_fallback(): void
    {
        config([
            'app.display_timezone' => 'Asia/Jakarta',
        ]);

        $value = CarbonImmutable::create(
            year: 2026,
            month: 8,
            day: 2,
            hour: 0,
            minute: 35,
            second: 0,
            timezone: 'UTC',
        );

        $this->assertSame(
            '2026-08-02 07:35:00',
            local_datetime(
                $value,
                'Y-m-d H:i:s',
            ),
        );

        $this->assertSame(
            'Belum dipasang',
            local_datetime(
                null,
                fallback: 'Belum dipasang',
            ),
        );
    }

    public function test_it_does_not_mutate_original_datetime(): void
    {
        config([
            'app.display_timezone' => 'Asia/Jakarta',
        ]);

        $value = Carbon::create(
            year: 2026,
            month: 8,
            day: 2,
            hour: 0,
            minute: 35,
            second: 0,
            timezone: 'UTC',
        );

        local_datetime($value);

        $this->assertSame(
            'UTC',
            $value->timezoneName,
        );

        $this->assertSame(
            '2026-08-02 00:35:00',
            $value->format('Y-m-d H:i:s'),
        );
    }

    public function test_it_supports_translated_datetime_format(): void
    {
        config([
            'app.locale' => 'id',
            'app.display_timezone' => 'Asia/Jakarta',
        ]);

        $value = CarbonImmutable::create(
            year: 2026,
            month: 8,
            day: 2,
            hour: 3,
            minute: 19,
            second: 58,
            timezone: 'UTC',
        );

        $this->assertSame(
            '02 Agustus 2026, 10.19',
            local_datetime(
                $value,
                'd F Y, H.i',
                translated: true,
            ),
        );
    }
}
