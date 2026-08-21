<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Public\LandingPage;

class PublicCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_successfully()
    {
        Livewire::test(LandingPage::class)
            ->assertStatus(200);
    }
}
