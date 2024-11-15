<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Mail;

use Illuminate\Console\Command;

class SendTestEmail extends Command
{
    protected $signature = 'email:send-test';
    protected $description = 'Send a test email using Mailtrap';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Mail::raw('Hello, this is a test email from Laravel using Mailtrap!', function ($message) {
            $message->to('wwwasyad@gmail.com')
                    ->subject('Test Email');
        });

        $this->info('Test email sent successfully!');
    }
}
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */

