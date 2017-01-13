<?php
declare(strict_types=1);

namespace Tests\App\Http\Controllers;

use TestCase;
use App\User;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UsersControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now('UTC'));
    }

    public function tearDown()
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    /** @test **/
    public function profile_return_status_code_404(): void
    {
        $user = factory(User::class)->create();
        $this->get('/users/profile/999999')->seeStatusCode(404);
    }

    /** @test **/
    public function profile_return_status_code_200(): void
    {
        $user = factory(User::class)->create();
        $this->get('/users/profile/' . $user->id)->seeStatusCode(200);
    }

    /** @test **/
    public function profile_should_return_item_of_record(): void
    {
        $user = factory(User::class)->create();
        
        $this->get('/users/profile/' . $user->id);

        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);

        $data = $content['data'];

        $this->assertEquals($user->id, $data['id']);
        $this->assertEquals($user->username, $data['username']);
        $this->assertEquals(
            $user->created_at->toIso8601String(),
            $data['created']
        );
        $this->assertEquals(
            $user->updated_at->toIso8601String(),
            $data['updated']
        );
    }

    /** @test **/
    public function get_list_return_status_code_200(): void
    {
        $this->get('/users/get_list')->seeStatusCode(200);
    }

    /** @test **/
    public function get_list_should_return_a_collection_of_records(): void
    {
        $users = factory(User::class, 2)->create();

        $this->get('/users/get_list');

        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);

        foreach ($users as $user) {
            $this->seeJson([
                'id'        => $user->id,
                'username'  => $user->username,
                'created'   => $user->created_at->toIso8601String(),
                'updated'   => $user->updated_at->toIso8601String(),
            ]);
        }
    }
}
