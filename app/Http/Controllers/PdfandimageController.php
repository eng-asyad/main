<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use App\Models\Book;

class PdfandimageController extends Controller
{
    public function upload(Request $request)
{
    $request->validate([
        'cover_image' => 'required|image',
        'pdf' => 'required|mimes:pdf',
    ]);
    $coverImagePath = null;
    if ($request->hasFile('cover_image')) {
        $coverImage = $request->file('cover_image');
        $coverImageName = time() . '.' . $coverImage->getClientOriginalExtension();
        
        // Store the image using the custom cover_images disk
        $coverImage->storeAs('cover_images', $coverImageName, 'cover_images');    
        $photoUrl = '/cover_images/' . $coverImageName;

        $coverImagePath = $photoUrl; 
    }  

    $pdfPath = null;
    if ($request->hasFile('pdf')) {
        $pdf = $request->file('pdf');
        $pdfName = time() . '.' . $pdf->getClientOriginalExtension();
        $pdfPath = $pdf->storeAs('public/pdfs', $pdfName);
        $pdfPath = Storage::url($pdfPath);
    }

    return response()->json([
        'cover_image' => $coverImagePath,
        'pdf' => $pdfPath,
    ]);
}


    public function download($filename)
    {
        $filePath = 'pdfs/' . $filename;
 
       if (!Storage::disk('public')->exists($filePath)) {
        abort(404);
    }
         return Storage::disk('public')->download($filePath);
    }


 

}
