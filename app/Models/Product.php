<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function inStock(): bool
    {
        return $this->stocks()->whereInStock(true)->exists();
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function track()
    {
        $this->stocks->each->track();
    }

    public function recordHistory(Stock $stock): void
    {
        $this->histories()->create([
            'stock_id' => $stock->id,
            'in_stock' => $stock->in_stock,
            'price' => $stock->price,
        ]);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(History::class);
    }
}
