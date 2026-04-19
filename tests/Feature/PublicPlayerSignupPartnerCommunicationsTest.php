<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPlayerSignupPartnerCommunicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_signup_form_displays_partner_communications_agreement(): void
    {
        $response = $this->get(route('player.signup.show'));

        $response->assertOk();
        $response->assertSee('Partner Communications');
        $response->assertSee('selected leisure partners may contact them');
        $response->assertSee('partner_communications_agreement');
    }

    public function test_partner_communications_agreement_is_required(): void
    {
        $response = $this->from(route('player.signup.show'))->post(route('player.signup.submit'), [
            'player_code_of_conduct_agreement' => '1',
            'parent_code_of_conduct_agreement' => '1',
        ]);

        $response->assertRedirect(route('player.signup.show'));
        $response->assertSessionHasErrors('partner_communications_agreement');
    }
}
