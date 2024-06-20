<?php

namespace App\Models;

use App\Clients\ClientFactory;
use App\Events\NowInStock;
use App\UseCases\StockTrack;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'in_stock' => 'boolean'
    ];

    public function track($callback = null)
    {

        (new StockTrack($this))->handle();

        // $status = $this->retailer
        //     ->client()
        //     ->checkAvailability($this);

        // if (!$this->in_stock && $status->available) {
        //     event(new NowInStock($this));
        // }

        // $this->update([
        //     'in_stock' => $status->available,
        //     'price' => $status->price,
        // ]);

        // $callback && $callback($this);
    }

    public function retailer(): BelongsTo
    {
        return $this->belongsTo(Retailer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
