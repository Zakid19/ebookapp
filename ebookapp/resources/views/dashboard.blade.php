<x-app-layout>
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-200 p-3 mb-4 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Upload Form -->
    <form action="{{ route('ebooks.upload') }}" method="POST" enctype="multipart/form-data" class="mb-6 flex flex-col md:flex-row gap-3 items-center">
        @csrf
        <input type="text" name="title" placeholder="Ebook Title" class="border p-2 rounded w-full md:w-1/3">
        <input type="file" name="pdf" class="border p-2 rounded w-full md:w-1/3">
        <button type="submit" class="bg-blue-500 text-white px-5 py-2 rounded w-full md:w-auto hover:bg-blue-600 transition">Upload</button>
    </form>

    <!-- Ebook Table -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border shadow-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">No</th>
                    <th class="border p-2">Nama Ebook</th>
                    <th class="border p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ebooks as $i => $ebook)
                <tr class="hover:bg-gray-50">
                    <td class="border p-2">{{ $i + 1 }}</td>
                    <td class="border p-2">{{ $ebook->title }}</td>
                    <td class="border p-2 flex gap-2">
                        <button onclick="viewEbook({{ $ebook->id }})" class="bg-green-500 px-3 py-1 text-white rounded hover:bg-green-600 transition">Lihat</button>
                        <form action="{{ route('ebooks.delete', $ebook) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 px-3 py-1 text-white rounded hover:bg-red-600 transition">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="border p-2 text-center text-gray-500">Belum ada Ebook</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PDF Viewer -->
    <div id="pdfViewer" class="mt-6 hidden">
    <div class="flex justify-between items-center mb-2">
        <h2 class="text-lg font-semibold">Preview Ebook</h2>
        <button onclick="closeViewer()" class="bg-gray-500 px-3 py-1 text-white rounded hover:bg-gray-600 transition">Close</button>
    </div>
    <div id="pdfCanvasContainer" class="w-full border rounded overflow-auto p-2 bg-gray-50" style="max-height:600px;"></div>
</div>
</div>

@section('scripts')
<!-- PDF.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.9.179/pdf.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentViewerId = null;

    window.viewEbook = function(id) {
        if(currentViewerId === id) return; // prevent reload same ebook

        fetch(`/ebooks/view/${id}`)
            .then(res => res.json())
            .then(data => renderPDF(data.url))
            .catch(err => alert("Gagal load PDF"));
    }

    function renderPDF(url) {
        const container = document.getElementById('pdfCanvasContainer');
        container.innerHTML = ''; // clear previous PDF
        document.getElementById('pdfViewer').classList.remove('hidden');

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.9.179/pdf.worker.min.js';

        pdfjsLib.getDocument(url).promise.then(pdf => {
            for(let i=1; i<=pdf.numPages; i++){
                pdf.getPage(i).then(page => {
                    const viewport = page.getViewport({scale:1.5});
                    const canvas = document.createElement('canvas');
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    container.appendChild(canvas);
                    page.render({canvasContext: canvas.getContext('2d'), viewport: viewport});
                });
            }
        });
        currentViewerId = url; // track current PDF
    }

    window.closeViewer = function() {
        document.getElementById('pdfCanvasContainer').innerHTML = '';
        document.getElementById('pdfViewer').classList.add('hidden');
        currentViewerId = null;
    }

    // Tab visibility detection
    document.addEventListener('visibilitychange', function() {
        if(document.hidden) alert("Warning: Jangan tinggalkan tab ini!");
    });
});
</script>
@endsection
</x-app-layout>
