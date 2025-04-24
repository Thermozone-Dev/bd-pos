<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateApiSecretKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-api-secret-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate secret key for api endpoint connections';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = app()->environmentFilePath();

        $secret_key = 'possikat'.str()->random(27).'key';

        $search = file_get_contents($path);
        $pattern = '/API_SECRET_KEY=""/i';
        $replace = 'API_SECRET_KEY="'.$secret_key.'"';

        if(preg_match($pattern,$search)){
            file_put_contents($path, preg_replace($pattern, $replace, $search));
            print("Secret Key Generated : ".$secret_key." \n");
        }else{
            print("Secret Key Already Set \n");
        }

    }
}
