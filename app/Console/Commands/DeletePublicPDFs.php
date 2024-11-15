<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Storage;


class DeletePublicPDFs extends Command
{
    protected $signature = 'clean:publicpdfs';

    protected $description = 'Deletes all PDF files in the public/pdf directory.';

    public function handle()
    {
        $directory = public_path('pdf');
        $files = File::allFiles($directory);

        foreach ($files as $file) {
            File::delete($file);
        }
        $directory = public_path('cover_image');
        $files = File::allFiles($directory);

        foreach ($files as $file) {
            File::delete($file);
        }

        $this->info('All PDF files in public/pdf have been deleted.');
    }
    

}
