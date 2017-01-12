<?php
declare(strict_types=1);

namespace Tests\App\Http\Controllers;

use TestCase;

class UsersControllerTest extends TestCase
{
    /**
     * Test response status code 200.
     *
     * @return void
     */
    public function testProfileStatusCode200(): void
    {
        $this->get('/users/profile')->seeStatusCode(200);
    }

    /**
     * Test response empty json
     *
     * @return void
     */
    public function testProfileEmptyJson(): void
    {
        $this->get('/users/profile')->seeJson([]);
    }

    /**
     * Test valid response users/get_list
     *
     * @return void
     */
    public function testValidResponseGetList(): void
    {
        $this->get('/users/get_list')->seeStatusCode(200);
        $data = json_decode($this->response->getContent(), true);

        if ($data) {
            $this->assertArrayHasKey('id', $data[0]);
            $this->assertArrayHasKey('username', $data[0]);
            $this->assertCount(2, array_keys($data[0]));
        }
    }
}
