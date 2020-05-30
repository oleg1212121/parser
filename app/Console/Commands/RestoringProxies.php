<?php

namespace App\Console\Commands;

use App\Models\Proxy;
use Illuminate\Console\Command;

class RestoringProxies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restoring:proxies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restoring failed proxies';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Proxy::forRestoring()->limit(500)->update([
            'fails' => 0
        ]);
    }
}
