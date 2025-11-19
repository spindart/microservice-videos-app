<?php

namespace Tests\Unit\App\Http\Controllers\Api;

use App\Http\Controllers\Api\CategoryController;
use Core\Application\DTO\Output\Category\ListCategoriesOutputDto;
use Core\Application\UseCase\Category\ListCategoriesUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Illuminate\Http\Request;
use Mockery\Mock;

class CategoryControllerUnitTes extends TestCase
{

    public function testIndex()
    {

        $mockRequest = Mockery::mock(Request::class);
        $mockRequest->shouldReceive('get')->andReturn('test');

        $mockDtoOutput = Mockery::mock(
            ListCategoriesOutputDto::class,
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


        $mockUseCase = Mockery::mock(ListCategoriesUseCase::class);
        $mockUseCase->shouldReceive('execute')->andReturn($mockDtoOutput);

        $categoryController = new CategoryController();
        $response = $categoryController->index($mockRequest, $mockUseCase);
        $this->assertIsObject($response->resource);
        $this->assertArrayHasKey('meta', $response->additional);

        /**
         * Spies
         */

        $mockUseCaseSpy = Mockery::spy(ListCategoriesUseCase::class);
        $mockUseCaseSpy->shouldReceive('execute')->andReturn($mockDtoOutput);
        $response = $categoryController->index($mockRequest, $mockUseCaseSpy);
        $mockUseCaseSpy->shouldHaveReceived('execute');

        Mockery::close();

    }
}
