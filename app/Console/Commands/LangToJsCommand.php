<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use JShrink\Minifier;
use File;

class LangToJsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meridian:lang';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Translation files to be used in Javascript App.';

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
        $langs = config('meridian.langs');

        Storage::disk('assets')->deleteDirectory('lang');
        Storage::disk('assets')->makeDirectory('lang');

        foreach($langs as $lang => $name) {
            $minifiedCode = Minifier::minify(
                $this->getContent($lang), ['flaggedComments' => false]
            );
            Storage::disk('assets')->put("lang/{$lang}.js", $minifiedCode);
        }

        $manifest = collect(array_keys($langs))->mapWithKeys(function($lang) {
            return ["/lang/{$lang}.js" => "/lang/{$lang}.js?id=".str_random(16)];
        })->toArray();

        Storage::disk('assets')->put("lang-manifest.json", json_encode($manifest, JSON_PRETTY_PRINT));

        $this->info('Lang files generated!');
    }

    protected function getContent($lang)
    {
        $json = '{}';
        $path = base_path("resources/lang/{$lang}/app.php");
        if(File::exists($path)) {
            $file = require $path;
            $json = json_encode($file);
        }



        $str = "
        (function () {
            T_FILES = {$json};
        })();
        ";
        return $str;
    }
}
