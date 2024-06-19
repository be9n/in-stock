<?php

namespace Tests\Unit;

use App\Clients\Client;
use App\Clients\ClientException;
use App\Clients\StockStatus;
use App\Models\Retailer;
use App\Models\Stock;
use Database\Seeders\RetailerWithProductSeeder;
use Facades\App\Clients\ClientFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class StockTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_throws_an_exception_if_a_client_is_not_found_when_tracking(): void
    {
        $this->seed(RetailerWithProductSeeder::class);

        Retailer::first()->update(['name' => 'Foo Retailer']);

        $this->expectException(ClientException::class);

        Stock::first()->track();
    }

    public function test_it_updates_local_stock_status_after_being_tracked()
    {
        $this->seed(RetailerWithProductSeeder::class);

        // $clientMock = Mockery::mock(Client::class);
        // $clientMock->shouldReceive('checkAvailability')->andReturn(
        //     new StockStatus(
        //         available: true,
        //         price: 99
        //     )
        // );
        // ClientFactory::shouldReceive('make')->andReturn($clientMock);

        ClientFactory::shouldReceive('make->checkAvailability')->andReturn(new StockStatus(available: true, price: 99));

        // ClientFactory::shouldReceive('make')->andReturn(new class implements Client {
        //     public function checkAvailability(Stock $stock): StockStatus
        //     {
        //         return new StockStatus(
        //             available: true,
        //             price: 99
        //         );
        //     }
        // });

        $stock = tap(Stock::first())->track();

        $this->assertTrue($stock->in_stock);
        $this->assertEquals(99, $stock->price);
    }
}
