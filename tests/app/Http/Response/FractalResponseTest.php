<?php

namespace Tests\App\Http\Response;

use TestCase;
use App\Http\Response\FractalResponse;
use Mockery as m;
use League\Fractal\Manager;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;

class FractalResponseTest extends TestCase
{
    /** @test **/
    public function it_can_be_initialized()
    {
        $manager    = m::mock(Manager::class);
        $serializer = m::mock(SerializerAbstract::class);

        $manager
            ->shouldReceive('setSerializer')
            ->with($serializer)
            ->once()
            ->andReturn($manager);

        $fractal = new FractalResponse($manager, $serializer);
        $this->assertInstanceOf(FractalResponse::class, $fractal);
    }

    /** @test **/
    public function it_can_transform_an_item()
    {
        // Transformer
        $transformer = m::mock(TransformerAbstract::class);

        // Scope
        $scope = m::mock(Scope::class);
        $scope
            ->shouldReceive('toArray')
            ->once()
            ->andReturn(['foo' => 'bar']);

        // Serializer
        $serializer = m::mock(SerializerAbstract::class);

        // Manager
        $manager = m::mock(Manager::class);
        $manager
            ->shouldReceive('setSerializer')
            ->with($serializer)
            ->once();
        $manager
            ->shouldReceive('createData')
            ->once()
            ->andReturn($scope);

        $fractal = new FractalResponse($manager, $serializer);

        $this->assertInternalType(
            'array',
            $fractal->item(['foo' => 'bar'], $transformer)
        );
    }

    /** @test **/
    public function it_can_transform_a_collection()
    {
        $data = [
                    ['foo' => 'bar'],
                    ['fizz' => 'buzz'],
                ];
        // Transformer
        $transformer = m::mock(TransformerAbstract::class);

        // Scope
        $scope = m::mock(Scope::class);
        $scope
            ->shouldReceive('toArray')
            ->once()
            ->andReturn($data);

        // Serializer
        $serializer = m::mock(SerializerAbstract::class);

        // Manager
        $manager = m::mock(Manager::class);
        $manager
            ->shouldReceive('setSerializer')
            ->with($serializer)
            ->once();
        $manager
            ->shouldReceive('createData')
            ->once()
            ->andReturn($scope);

        $fractal = new FractalResponse($manager, $serializer);

        $this->assertInternalType(
            'array',
            $fractal->collection($data, $transformer)
        );
    }
}
