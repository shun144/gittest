<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendMail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '(test) send mail';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo 'Send an kano!!';
    }
}
