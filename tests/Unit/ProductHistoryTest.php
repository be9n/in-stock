<?php

namespace Tests\Unit;

use App\Clients\StockStatus;
use App\Models\History;
use App\Models\Product;
use App\Models\Stock;
use Database\Seeders\RetailerWithProductSeeder;
use Facades\App\Clients\ClientFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_records_history_every_time_stock_is_tracked(): void
    {
        $this->seed(RetailerWithProductSeeder::class);

        ClientFactory::shouldReceive("make->checkAvailability")
            ->andReturn(new StockStatus(available: true, price: 9900));

        // Http::fake(fn() => ['salePrice' => 9900, 'onlineAvailability' => true]);

        $product = tap(Product::first(), function ($product) {

            $this->assertCount(0, $product->histories);

            $product->track();

            $this->assertCount(1, $product->refresh()->histories);
        });


        $history = $product->histories->first();
        $stock = $product->stocks()->first();

        $this->assertEquals($stock->price, $history->price);
        $this->assertEquals($stock->in_stock, $history->in_stock);
        $this->assertEquals($stock->product_id, $history->product_id);
        $this->assertEquals($stock->id, $history->stock_id);
    }
}
