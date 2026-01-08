<div class="flex items-center">
    <button
        type="button"
        {{-- This triggers Filament's internal search modal --}}
        x-on:click="$dispatch('open-global-search')"
        class="flex items-center justify-center w-9 h-9 rounded-lg text-gray-400 hover:text-primary-500 hover:bg-white/5 transition-all duration-200"
        title="Search (Ctrl+K)"
    >
        {{-- Minimalist Search Icon --}}
        <x-filament::icon
            icon="heroicon-o-magnifying-glass"
            class="w-5 h-5"
        />
    </button>
</div>
