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

    private $users = [];

    public function setUp()
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now('UTC'));
        $this->users = factory(User::class, 10)->create();
    }

    public function tearDown()
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    /** @test **/
    public function get_return_status_code_404(): void
    {
        $this->get('/users/999999')->seeStatusCode(404);
    }

    /** @test **/
    public function get_return_status_code_200(): void
    {
        $this->get('/users/' . $this->users[0]->id)->seeStatusCode(200);
    }

    /** @test **/
    public function get_should_return_item_of_record(): void
    {
        $user = $this->users[0];
        
        $this->get('/users/' . $user->id);

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
        $this->get('/users')->seeStatusCode(200);
    }

    /** @test **/
    public function get_list_should_return_a_collection_of_records(): void
    {
        $this->get('/users');

        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);

        foreach ($this->users as $user) {
            $this->seeJson([
                'id'        => $user->id,
                'username'  => $user->username,
                'created'   => $user->created_at->toIso8601String(),
                'updated'   => $user->updated_at->toIso8601String(),
            ]);
        }
    }

    /***
     |
     | Store user
     |
     ***/

    /** @test **/
    public function store_should_save_new_user_in_the_database(): void
    {
        $this->post('/users', [
            'username' => 'user.test.1',
            'password' => '123456'
        ]);

        $this->seeJson([
                'created' => true
            ])
            ->seeInDatabase('users', [
                   'username' => 'user.test.1'
            ]);
    }

    /** @test **/
    public function store_should_respond_with_a_201_and_location_header_when_successful(): void
    {
        $this->post('/users', [
            'username' => 'user.test.1',
            'password' => '123456'
        ]);

        $this->seeStatusCode(201)
             ->seeHeaderWithRegExp('Location', '#/users/[\d]+$#');
    }
}
