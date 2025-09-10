<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use PhpParser\Node\Expr\Throw_;

class ResetSalesInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-sales-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resetting of sales invoice batch';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        try {
            $last_transaction = Transaction::orderBy('id','desc')->first();
            if($last_transaction){
                if($last_transaction->last_reseted){

                    $this->error('Duplicate Resetting Detected'. PHP_EOL);

                    return 1;
                }
                if(!$last_transaction->reset_si_batch){
                    $last_transaction->reset_si_batch = 0;
                }
                $last_transaction->last_reseted = now()->format('Y-m-d H:i:s');
                $last_transaction->update();
                $this->info(PHP_EOL . 'Completed.' . PHP_EOL);
                return 0;
            }
            else {
                $this->error('Nothing to reset'. PHP_EOL);
                return 1;
            }
        }

        catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage() . PHP_EOL);
            return 1;

        }
    }
}
