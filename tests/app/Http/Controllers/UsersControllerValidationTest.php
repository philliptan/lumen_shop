<?php
declare(strict_types=1);

namespace Tests\App\Http\Controllers;

use TestCase;
use App\User;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Http\Response;

class UsersControllerValidationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test **/
    public function it_validates_required_fields_when_creating_a_new_user(): void
    {
        $this->post('/users', [], ['Accept' => 'application/json']);

        $this->assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY, 
            $this->response->getStatusCode()
        );

        $body = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('username', $body);
        $this->assertArrayHasKey('password', $body);

        $this->assertEquals(["The username field is required."], $body['username']);
        $this->assertEquals(["The password field is required."], $body['password']);
    }

    /** @test **/
    public function it_validates_requied_fields_when_updating_a_user(): void
    {

    }
}