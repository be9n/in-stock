<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Retailer;
use App\Models\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_checks_stock_for_products_at_retailers(): void
    {
        $product = Product::create(['name' => 'Nintendo Switch']);

        $bestBuy = Retailer::create(['name' => 'Best Buy']);

        $this->assertFalse($product->inStock());

        $stock = new Stock([
            'price' => 1000,
            'url' => 'http://foo.com',
            'sku' => '12345',
            'in_stock' => true,
        ]);

        $bestBuy->addStock($product, $stock);

        $this->assertTrue($product->inStock());

    }
}
