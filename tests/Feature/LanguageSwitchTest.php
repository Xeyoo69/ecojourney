<?php

namespace Tests\Feature;

use Tests\TestCase;

class LanguageSwitchTest extends TestCase
{
    public function test_user_can_switch_language_to_indonesian()
    {
        $response = $this->get('/lang/id');

        $response->assertRedirect();
        $this->assertEquals('id', session('locale'));
    }

    public function test_user_can_switch_language_to_english()
    {
        $response = $this->get('/lang/en');

        $response->assertRedirect();
        $this->assertEquals('en', session('locale'));
    }
}
