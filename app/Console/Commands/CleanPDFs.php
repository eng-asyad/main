<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanPDFs extends Command
{

protected $signature = 'clean:pdfs';

protected $description = 'Deletes all PDF files from the storage/app/public directory';

public function handle()
{
    // حذف ملفات PDF والمستندات
    $this->deleteFiles('pdfs', ['pdf', 'docx']);

    // حذف صور الغلاف
    $this->deleteFiles('cover_images', ['png', 'bmp', 'jpg']);
    $this->deleteFiles('user_photos', ['png', 'bmp', 'jpg']);

    $this->info('All specified files have been deleted.');
}

private function deleteFiles($directory, array $extensions)
{
    $files = Storage::disk('public')->allFiles($directory);

    foreach ($files as $file) {
        if (in_array(pathinfo($file, PATHINFO_EXTENSION), $extensions)) {
            Storage::disk('public')->delete($file);
            $this->info("Deleted: {$file}");
        }

    $this->info('All PDF files have been deleted.');
}
}
}