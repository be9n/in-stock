<?php

namespace Tests\Unit;

use App\Clients\BestBuy;
use App\Clients\StockStatus;
use App\Models\Stock;
use Database\Seeders\RetailerWithProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BestBuyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_tracks_a_product(): void
    {
        $this->seed(RetailerWithProductSeeder::class);

        $stock = tap(Stock::first())->update([
            'sku' => '6364253',
        ]);

        try {
            $stockStatus = (new BestBuy())->checkAvailability($stock);

            $this->assertInstanceOf(StockStatus::class, $stockStatus);
        } catch (\Exception $e) {
            $this->fail('Failed to track the BestBuy API properly, ' . $e->getMessage());
        }
    }
}
