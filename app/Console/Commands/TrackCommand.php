<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class TrackCommand extends Command
{

    protected $signature = 'track';

    protected $description = 'Track all product stock';

    public function handle()
    {
        // $bar = $this->output->createProgressBar();
        // $bar->start();
        // $this->output->progressStart($products->count());

        Product::all()
            ->tap(fn($products) => $this->output->progressStart($products->count()))
            ->each(function ($product) {
                $product->track();
                $this->output->progressAdvance();
            });

        $this->output->progressFinish();

        $this->showResults();

        $this->info('All done!');
    }

    protected function showResults()
    {
        $data = Product::query()
            ->leftJoin('stocks', 'stocks.product_id', '=', 'products.id')
            ->get($this->tableKeys());

        $this->table(
            array_map('ucwords', $this->tableKeys()),
            $data
        );
    }

    public function tableKeys(): array
    {
        return ['name', 'price', 'url', 'in_stock'];
    }
}
