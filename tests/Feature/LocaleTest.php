<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_english_is_default_locale(): void
    {
        $this->assertEquals('en', config('app.locale'));
    }

    public function test_french_translations_are_available(): void
    {
        $this->assertFileExists(lang_path('fr.json'));
        $translations = json_decode(file_get_contents(lang_path('fr.json')), true);
        $this->assertEquals('Tableau de bord', $translations['Dashboard']);
    }

    public function test_locale_can_be_switched(): void
    {
        $this->seedAccountingDefaults();
        $admin = \App\Models\User::where('email', 'admin')->first();

        $this->actingAs($admin)
            ->get(route('locale.switch', 'fr'))
            ->assertRedirect();

        $this->assertEquals('fr', session('locale'));
    }
}
