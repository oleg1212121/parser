<?php

namespace App\Jobs;

use App\Models\Proxy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RestoringProxies implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     * Порченные прокси у которых последняя дата обновления была более часа назад - восстанавливаются
     *
     * @return void
     */
    public function handle()
    {
        // todo: удалить job т.к. есть комманда

        Proxy::forRestoring()->limit(500)->update([
            'fails' => 0
        ]);
    }
}
