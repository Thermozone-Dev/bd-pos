<?php

namespace App\Console\Commands;

use App\Journal\Journal;
use Illuminate\Console\Command;
use PHPUnit\Event\Runtime\PHP;

class JournalTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'journal:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the journal functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            echo 'Testing journal functionality...'. PHP_EOL;
            if (!Journal::checkIfJournalExists()) {
                echo 'Journal does not exist, creating a new one.'. PHP_EOL;

                Journal::append('Journal Created.');
                echo 'Message appended successfully.';

                $content = Journal::read();
                echo 'Journal content:'. PHP_EOL;
                echo PHP_EOL . $content;
            } else {
                echo 'Journal exists, appending a new entry.'. PHP_EOL;

                echo Journal::getJournalPath() . PHP_EOL;

                Journal::append('Journal Log Append.');
                echo 'Single Message appended successfully.';

                Journal::appendList([
                    'First message in the list.',
                    'Second message in the list.',
                    'Third message in the list.',
                ]);

                $content = Journal::read();
                echo 'Journal content:'. PHP_EOL;
                echo PHP_EOL . $content;
            }
        }

        catch (\Exception $e) {
            echo 'An error occurred: ' . $e->getMessage() . PHP_EOL;
        } finally {
            echo PHP_EOL . 'Journal test completed.' . PHP_EOL;
        }

    }
}
