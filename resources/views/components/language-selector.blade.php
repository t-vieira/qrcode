@props(['class' => '', 'showLabel' => true, 'showFlag' => true])

@php
    $currentLocale = app()->getLocale();
    $availableLocales = config('translation.available_locales', []);
    $localeNames = [
        'pt_BR' => 'Português (Brasil)',
        'en' => 'English',
    ];
@endphp

<div class="language-selector {{ $class }}" x-data="{ open: false }">
    @if($showLabel)
        <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ __('messages.navigation.language') }}
        </label>
    @endif
    
    <div class="relative">
        <button @click="open = !open" 
                class="flex items-center justify-between w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <div class="flex items-center">
                @if($showFlag)
                    <span class="mr-2 text-lg">
                        @switch($currentLocale)
                            @case('pt_BR')
                                🇧🇷
                                @break
                            @case('en')
                                🇺🇸
                                @break
                            @default
                                🌐
                        @endswitch
                    </span>
                @endif
                <span>{{ $localeNames[$currentLocale] ?? $currentLocale }}</span>
            </div>
            <svg class="w-4 h-4 ml-2 text-gray-400" 
                 :class="{ 'rotate-180': open }" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             @click.away="open = false"
             class="absolute right-0 z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg">
            @foreach($availableLocales as $locale => $name)
                <a href="{{ route('locale.change', $locale) }}" 
                   class="flex items-center px-3 py-2 text-sm hover:bg-gray-100 {{ $locale === $currentLocale ? 'bg-primary-50 text-primary-700' : 'text-gray-700' }}">
                    @if($showFlag)
                        <span class="mr-2 text-lg">
                            @switch($locale)
                                @case('pt_BR')
                                    🇧🇷
                                    @break
                                @case('en')
                                    🇺🇸
                                    @break
                                @default
                                    🌐
                            @endswitch
                        </span>
                    @endif
                    <span>{{ $name }}</span>
                    @if($locale === $currentLocale)
                        <svg class="w-4 h-4 ml-auto text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

<style>
.language-selector .rotate-180 {
    transform: rotate(180deg);
}
</style>
