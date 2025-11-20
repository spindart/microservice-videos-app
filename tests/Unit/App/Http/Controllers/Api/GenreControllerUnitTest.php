<?php

namespace Tests\Unit\App\Http\Controllers\Api;

use App\Http\Controllers\Api\GenreController;
use Core\Application\DTO\Output\Genre\ListGenresOutputDto;
use Core\Application\UseCase\Genre\ListCategoriesUseCase;
use Core\Application\UseCase\Genre\ListGenresUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Illuminate\Http\Request;

class GenreControllerUnitTes extends TestCase
{

    public function testIndex()
    {

        $mockRequest = Mockery::mock(Request::class);
        $mockRequest->shouldReceive('get')->andReturn('test');

        $mockDtoOutput = Mockery::mock(
            ListGenresOutputDto::class,
            [
                [],
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1
            ]
        );


        $mockUseCase = Mockery::mock(ListGenresUseCase::class);
        $mockUseCase->shouldReceive('execute')->andReturn($mockDtoOutput);

        $GenreController = new GenreController();
        $response = $GenreController->index($mockRequest, $mockUseCase);
        $this->assertIsObject($response->resource);
        $this->assertArrayHasKey('meta', $response->additional);

        /**
         * Spies
         */

        $mockUseCaseSpy = Mockery::spy(ListGenresUseCase::class);
        $mockUseCaseSpy->shouldReceive('execute')->andReturn($mockDtoOutput);
        $response = $GenreController->index($mockRequest, $mockUseCaseSpy);
        $mockUseCaseSpy->shouldHaveReceived('execute');

        Mockery::close();

    }
}
