<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
     protected $fillable = ['title', 'file_path'];

    public function getUrlAttribute()
    {
        // signed route to stream pdf
        return route('ebooks.stream', ['ebook' => $this->id, 'signature' => \Illuminate\Support\Facades\URL::signedRoute('ebooks.stream', ['ebook' => $this->id])]);
    }

    public function filePath()
    {
        return storage_path('app/ebooks/' . $this->filename);
    }
}
