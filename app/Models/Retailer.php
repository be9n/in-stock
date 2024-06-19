<?php

namespace App\Models;

use App\Clients\Client;
use Facades\App\Clients\ClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retailer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function addStock(Product $product, Stock $stock)
    {
        $stock->product_id = $product->id;
        $this->stock()->save($stock);
    }

    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function client(): Client
    {
        return ClientFactory::make($this);
    }
}
