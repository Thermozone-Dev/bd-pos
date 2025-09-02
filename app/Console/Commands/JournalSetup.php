<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class JournalSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'journal:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo 'Setting up the journal...'. PHP_EOL;

        // Check if the journal directory exists, if not create it
        if (!Storage::exists('journal/')) {
            Storage::makeDirectory('journal/');
            echo 'Journal directory created.'. PHP_EOL;
        } else {
            echo 'Journal directory already exists.'. PHP_EOL;
        }

        // Check if the journal file exists, if not create it
        if (!Storage::exists('journal/journal.log')) {
            Storage::put('journal/journal.log', '');
            echo 'Journal file created.'. PHP_EOL;
        } else {
            echo 'Journal file already exists.'. PHP_EOL;
        }

        // Confirm setup completion
        echo 'Journal setup completed successfully.'. PHP_EOL;
    }
}
