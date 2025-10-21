@extends('dashboard')

@section('content')
<div class="mt-6">
    <h2 class="text-2xl font-semibold mb-4">{{ $ebook->title }}</h2>

    <div class="relative border rounded shadow">
        <iframe
            src="{{ asset('storage/ebooks/' . $ebook->file_name) }}#toolbar=0"
            width="100%" height="600"
            class="border-0"
            id="pdfFrame">
        </iframe>
    </div>
</div>

<script>
document.addEventListener("visibilitychange", function() {
    if (document.hidden) {
        alert("⚠️ Jangan berpindah tab! Tetap fokus membaca eBook ini.");
    }
});

// Blok klik kanan dan shortcut copy
document.addEventListener("contextmenu", e => e.preventDefault());
document.addEventListener("keydown", e => {
    if (e.ctrlKey && (e.key === "s" || e.key === "c" || e.key === "p")) {
        e.preventDefault();
        alert("⚠️ Aksi ini dinonaktifkan!");
    }
});
</script>
@endsection
