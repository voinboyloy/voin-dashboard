<?php

namespace Tests\Unit;

use App\Models\Credential;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class CredentialTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_encrypts_the_password()
    {
        $user = User::factory()->create();

        $credential = Credential::create([
            'user_id' => $user->id,
            'platform' => 'Facebook',
            'username' => 'testuser',
            'password' => 'secret123',
        ]);

        // Accessing the attribute via the model should return decrypted value
        $this->assertEquals('secret123', $credential->password);

        // Accessing the database directly should show encrypted value
        $rawPassword = DB::table('credentials')->where('id', $credential->id)->value('password');
        $this->assertNotEquals('secret123', $rawPassword);
        
        // Ensure it's not empty and looks like encrypted data
        $this->assertNotEmpty($rawPassword);
    }

    public function test_it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $credential = Credential::create([
            'user_id' => $user->id,
            'platform' => 'Twitter',
            'password' => 'mytwitterpass',
        ]);

        $this->assertInstanceOf(User::class, $credential->user);
        $this->assertEquals($user->id, $credential->user->id);
    }

    public function test_user_can_have_many_credentials()
    {
        $user = User::factory()->create();
        Credential::create([
            'user_id' => $user->id,
            'platform' => 'Insta',
            'password' => 'pass1',
        ]);
        Credential::create([
            'user_id' => $user->id,
            'platform' => 'LinkedIn',
            'password' => 'pass2',
        ]);

        $this->assertCount(2, $user->credentials);
    }
}
