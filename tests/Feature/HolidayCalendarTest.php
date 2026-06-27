<?php

namespace Tests\Feature;

use Tests\TestCase;

class HolidayCalendarTest extends TestCase
{
    public function test_it_returns_the_official_2026_holiday_calendar(): void
    {
        $response = $this->getJson('/holidays?year=2026');

        $response->assertOk()
            ->assertJsonCount(25)
            ->assertJsonFragment([
                'title' => 'Proklamasi Kemerdekaan',
                'start' => '2026-08-17',
            ])
            ->assertJsonFragment([
                'title' => 'Kelahiran Yesus Kristus',
                'start' => '2026-12-24',
                'extendedProps' => ['type' => 'Cuti Bersama'],
            ]);
    }

    public function test_it_returns_an_empty_calendar_for_an_unconfigured_year(): void
    {
        $this->getJson('/holidays?year=2024')
            ->assertOk()
            ->assertExactJson([]);
    }
}
