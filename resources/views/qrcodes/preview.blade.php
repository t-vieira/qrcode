<div class="w-24 h-24 bg-white border border-gray-200 rounded-lg flex items-center justify-center">
    @if($preview_url)
        <img src="{{ $preview_url }}" alt="QR Code Preview" class="w-20 h-20 object-contain">
    @else
        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
            <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
        </svg>
    @endif
</div>
