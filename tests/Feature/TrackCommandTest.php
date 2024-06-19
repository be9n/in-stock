<?php

namespace Tests\Feature;

use App\Clients\StockStatus;
use App\Models\Product;
use App\Models\Retailer;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\ImportantStockUpdateNotification;
use Database\Seeders\RetailerWithProductSeeder;
use Facades\App\Clients\ClientFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TrackCommandTest extends TestCase
{
    use RefreshDatabase;

    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     Notification::fake();

    //     $this->seed(RetailerWithProductSeeder::class);        
    // }

    public function test_it_tracks_product_stock(): void
    {
        $this->seed(RetailerWithProductSeeder::class);

        $this->assertFalse(Product::first()->inStock());

        ClientFactory::shouldReceive("make->checkAvailability")
            ->andReturn(new StockStatus(available: true, price: 9900));

        $this->artisan('track')
            ->expectsOutput('All done!');

        $this->assertTrue(Product::first()->inStock());
    }

    public function test_it_does_not_notify_when_the_stock_remains_as_it_is()
    {
        Notification::fake();

        // Given I have a user
        // And a product that is out of stock
        $this->seed(RetailerWithProductSeeder::class);

        $this->mockClientRequest(false, 22000);
            
        // When I track that product
        $this->artisan('track');

        // If the stock changes in a notable way after being tracked
        // Then the user should be notified
        Notification::assertNothingSent();
    }

    public function test_it_notifies_the_user_when_the_stock_is_now_available()
    {
        Notification::fake();

        // Given I have a user
        // And a product that is out of stock
        $this->seed(RetailerWithProductSeeder::class);

        $this->mockClientRequest(true, 9900);
            
        // When I track that product
        $this->artisan('track');

        // If the stock changes in a notable way after being tracked
        // Then the user should be notified
        Notification::assertSentTo(User::first(), ImportantStockUpdateNotification::class);
    }

    public function mockClientRequest($available, $price){
        ClientFactory::shouldReceive("make->checkAvailability")
        ->andReturn(new StockStatus($available, price: $price));
    }
}
