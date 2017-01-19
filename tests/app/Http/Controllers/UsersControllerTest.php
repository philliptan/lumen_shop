<?php
declare(strict_types=1);

namespace Tests\App\Http\Controllers;

use TestCase;
use App\User;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Http\Response;

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
        $this->users = [];
    }

    /** @test **/
    public function get_return_status_code_404(): void
    {
        $this->get('/users/999999999')->seeStatusCode(Response::HTTP_NOT_FOUND);
    }

    /** @test **/
    public function get_return_status_code_200(): void
    {
        $this->get('/users/' . $this->users[0]->id)->seeStatusCode(Response::HTTP_OK);
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
        $this->get('/users')->seeStatusCode(Response::HTTP_OK);
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

        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);

        $data = $content['data'];

        $this->assertTrue($data['id'] > 0, 'Expected a positive integer, but did not see one.');
        $this->assertEquals('user.test.1', $data['username']);
        $this->assertArrayHasKey('created', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['created']);
        $this->assertArrayHasKey('updated', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['updated']);

        $this
            ->seeInDatabase('users', [
                   'username' => 'user.test.1',
                   'password' => '123456',
                   'id' => $data['id'],
            ]);
    }

    /** @test **/
    public function store_should_respond_with_a_201_and_location_header_when_successful(): void
    {
        $this->post('/users', [
            'username' => 'user.test.2',
            'password' => '123456'
        ]);

        $this->seeStatusCode(Response::HTTP_CREATED)
             ->seeHeaderWithRegExp('Location', '#/users/[\d]+$#');
    }

    /***
     |
     | Update user
     |
     ***/

    /** @test **/
    public function update_should_only_change_fillable_fields(): void
    {
        $user  = $this->users[0];
        $user1 = $this->users[1];

        $this->put('/users/' . $user->id, [
            'id'       => $user1->id,
            'username' => 'username.test.update',
            'password' => 'password.test.update'
        ]);

        $this->seeStatusCode(Response::HTTP_OK)
            ->seeInDatabase('users', [
                'id' => $user->id,
                'username' => 'username.test.update',
                'password' => 'password.test.update'
            ]);

        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);

        $data = $content['data'];

        $this->assertArrayHasKey('created', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['created']);
        $this->assertArrayHasKey('updated', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['updated']);

    }

    /** @test **/
    public function update_should_fail_with_an_duplicate_username(): void
    {
        $user  = $this->users[0];
        $user1 = $this->users[1];

        $this->put('/users/' . $user->id, [
            'username' => $user1->username
        ]);

        $this->seeStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('message', $content);
    }

    /** @test **/
    public function update_should_fail_with_an_invalid_id(): void
    {
        $this->put('/users/999999999', [
            'password' => 'password.test.update'
        ]);

        $this->seeStatusCode(Response::HTTP_NOT_FOUND);
        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('message', $content);
    }

    /** @test **/
    public function update_should_not_match_an_invalid_route(): void
    {
        $this->put('/users/this-is-invalid')->seeStatusCode(Response::HTTP_NOT_FOUND);
    }

    /***
     |
     | Delete user
     |
     ***/

    /** @test **/
    public function destroy_should_remove_a_valid_user(): void
    {
        $user = $this->users[9];
        $this->delete('users/' . $user->id)
            ->seeStatusCode(Response::HTTP_NO_CONTENT)
            ->isEmpty();

        $this->notSeeInDatabase('users', [
            'id' => $user->id,
            'deleted_at' => null
        ]);
    }

    /** @test **/
    public function destroy_should_return_a_404_with_an_invalid_id(): void
    {
        $this->delete('/users/999999999')
            ->seeStatusCode(Response::HTTP_NOT_FOUND);
    }

    /** @test **/
    public function destroy_should_not_match_an_invalid_route(): void
    {
        $this->delete('/users/this-is-invalid')
            ->seeStatusCode(Response::HTTP_NOT_FOUND);
    }
}
