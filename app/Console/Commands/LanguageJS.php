<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
class LanguageJS extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:lang-js';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create language for javascript';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->process(App::currentLocale());

        $this->info('Language for javascript has been created!');
    }

    public function process($lang)
    {
        $files   = glob(resource_path('lang/' . $lang . '/*.php'));
        $strings = [];

        //Import only custom files
        $importFiles = ['message' , 'app'];

        foreach ($files as $file) {
            $fileName = basename($file, '.php');
            //Remove unnecessary files
            if (!in_array($fileName, $importFiles)) {
                continue;
            }
            $strings[$fileName] = require $file;
        }

        $content = 'window.i18n = ' . json_encode($strings) . ';';
        File::put(public_path('/assets/js/'. $lang . '.js'), $content);
    }
}
