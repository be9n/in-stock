<?php

namespace App\UseCases;

use App\Clients\StockStatus;
use App\Models\History;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\ImportantStockUpdateNotification;

class StockTrack
{
    public StockStatus $stockStatus;

    public function __construct(public Stock $stock)
    {

    }

    public function handle()
    {
        $this->checkAvailability();
        $this->notifyUser();
        $this->updateStock();
        $this->recordToHistory();
    }

    public function checkAvailability()
    {
        $this->stockStatus = $this->stock->retailer
            ->client()
            ->checkAvailability($this->stock);
    }

    public function notifyUser()
    {
        if (!$this->stock->in_stock && $this->stockStatus->available) {
            User::first()->notify(new ImportantStockUpdateNotification($this->stock));
        }
    }

    public function updateStock()
    {
        $this->stock->update([
            'in_stock' => $this->stockStatus->available,
            'price' => $this->stockStatus->price,
        ]);
    }
    public function recordToHistory()
    {
        History::create([
            'product_id' => $this->stock->product_id,
            'stock_id' => $this->stock->id,
            'in_stock' => $this->stock->in_stock,
            'price' => $this->stock->price,
        ]);
    }
}
