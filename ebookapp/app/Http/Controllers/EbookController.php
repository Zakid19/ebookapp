<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class EbookController extends Controller
{
    // Dashboard
    public function index() {
        $ebooks = Ebook::orderBy('created_at', 'desc')->get(); // newest first
        return view('dashboard', compact('ebooks'));
    }

    // Upload PDF
    public function upload(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'pdf' => 'required|mimes:pdf|max:10240', // max 10MB
        ]);

        $file = $request->file('pdf');
        $fileName = time().'_'.$file->getClientOriginalName();
        $filePath = $file->storeAs('ebooks', $fileName);

        Ebook::create([
            'title' => $request->title,
            'file_path' => $filePath,
        ]);

        return back()->with('success', 'Ebook uploaded successfully!');
    }

    // Generate signed URL for PDF viewer
    public function view(Ebook $ebook) {
        $url = URL::signedRoute('ebook.stream', ['ebook' => $ebook->id]);
        return response()->json(['url' => $url]);
    }

    // Stream PDF inline
    public function stream(Ebook $ebook) {
    // cek signed URL & login
    if (!request()->hasValidSignature() || !auth()->check()) {
        abort(401);
    }

    // ambil path fisik
    $fullPath = Storage::path($ebook->file_path);

    if (!file_exists($fullPath)) {
        abort(404, "File tidak ditemukan: {$fullPath}");
    }

    return response()->file($fullPath, [
        'Content-Disposition' => 'inline',
        'X-Content-Type-Options' => 'nosniff',
        'Cache-Control' => 'no-store, no-cache, must-revalidate',
    ]);
}


    // Delete PDF + DB record
    public function delete(Ebook $ebook) {
        if (Storage::exists($ebook->file_path)) {
            Storage::delete($ebook->file_path);
        }
        $ebook->delete();

        return back()->with('success', 'Ebook deleted!');
    }
}
