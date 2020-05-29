<?php

namespace App\Jobs;

use App\Models\Link;
use App\Models\Page;
use App\Services\ParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateLinksFromCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page;

    /**
     * Create a new job instance.
     *
     * @param Page $page
     * @return void
     */
    public function __construct(Page $page = null)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->page){
            $parser = (new ParserService($this->page))->parsingLinks();
            if($parser->outputNextLink){
                Link::create(['link' => $parser->outputNextLink]);
            }
            if($parser->outputLinks){
                foreach ($parser->outputLinks as $outputLink) {
                    Link::create($outputLink);
                }
            }
        }
    }
//
//if ($nextLink) {
//
//$link = Link::firstOrCreate([
//'link' => $this->domen.$nextLink
//]);
//if($link->is_done = 0){
//GetPage::dispatch($link);
//}
//}
}
