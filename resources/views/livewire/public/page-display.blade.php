<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <!-- Header -->
            <div class="mb-6 pb-4 border-b border-gray-100 text-center">
                <h1 class="text-3xl font-black text-gray-900 tracking-tight italic uppercase mb-2">{{ $page->title }}</h1>
            </div>

            <!-- Content -->
            <div class="prose prose-indigo max-w-none text-gray-600 leading-relaxed space-y-4">
                {!! $page->content_html !!}
            </div>
        </div>
    </div>
</div>
