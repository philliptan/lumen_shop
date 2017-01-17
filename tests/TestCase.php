<?php

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function setUp()
    {
        parent::setUp();

        DB::listen(function($query) {
            $message = "\n SQL ::: %s \n Binding ::: %s \n Timing ::: %s";
            Log::debug(sprintf(
                $message,
                $query->sql,
                "[" . implode(", ", $query->bindings) . "]",
                $query->time
            ));
        });
    }

    /**
     * See if the response has a header.
     *
     * @param $header
     * @return $this
     */
    public function seeHasHeader($header)
    {
        $this->assertTrue(
            $this->response->headers->has($header),
            "Response should have the header '{$header}' but does not."
        );

        return $this;
    }

    /**
     * Asserts that the response header matches a given regular expression
     *
     * @param $header
     * @return $this
     */
    public function seeHeaderWithRegExp($header, $regexp)
    {
        $this->seeHasHeader($header)
            ->assertRegExp(
                $regexp,
                $this->response->headers->get($header)
            );

        return $this;
    }
}
