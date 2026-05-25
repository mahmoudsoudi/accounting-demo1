<?php

namespace Tests\Feature;

use App\Models\Account;
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

    public function test_arabic_translations_are_available(): void
    {
        $this->assertFileExists(lang_path('ar.json'));
        $translations = json_decode(file_get_contents(lang_path('ar.json')), true);
        $this->assertEquals('لوحة التحكم', $translations['Dashboard']);
        $this->assertEquals('دليل الحسابات', $translations['Chart of Accounts']);
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

    public function test_arabic_locale_can_be_switched(): void
    {
        $this->seedAccountingDefaults();
        $admin = \App\Models\User::where('email', 'admin')->first();

        $this->actingAs($admin)
            ->get(route('locale.switch', 'ar'))
            ->assertRedirect();

        $this->assertEquals('ar', session('locale'));
    }

    public function test_chart_of_accounts_displays_translated_names(): void
    {
        $this->seedAccountingDefaults();

        app()->setLocale('ar');

        $cashAccount = Account::where('code', '1010')->first();

        $this->assertNotNull($cashAccount);
        $this->assertEquals('النقد في الصندوق', $cashAccount->name);
        $this->assertStringContainsString('النقد في الصندوق', $cashAccount->full_name);

        app()->setLocale('fr');

        $this->assertEquals('Caisse', $cashAccount->fresh()->name);
    }
}
