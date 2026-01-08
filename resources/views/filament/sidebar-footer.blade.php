@php
    $network = $stats['network'];
    $os = $stats['os'];
    $cpu = $stats['cpu'];
    $ipAddress = $ip ?? '127.0.0.1';
@endphp

<div
    x-data="{}"
    x-show="$store.sidebar.isOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    class="w-full"
>
    <div wire:poll.10s class="px-6 py-6 mt-auto space-y-6 border-t border-white/10  text-gray-300">

        {{-- Copyable IP Section --}}
        <div class="space-y-2" x-data="{
            copyText: '{{ $ipAddress }}',
            copied: false,
            copy() {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(this.copyText);
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                }
            }
        }">
            <span class="text-[8px] font-black uppercase text-gray-500 tracking-[0.2em] ml-1">IP Gateway</span>
            <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/5 border border-white/10 shadow-inner group cursor-pointer hover:border-primary-500/50 transition-all duration-300"
                 @click="copy()">

                <code class="text-[11px] font-mono font-bold text-gray-300 group-hover:text-primary-400 transition-colors">
                    {{ $ipAddress }}
                </code>

                <div class="relative flex items-center justify-center">
                    <x-filament::icon x-show="!copied" icon="heroicon-o-clipboard-document" class="w-4 h-4 text-gray-600 group-hover:text-primary-500 transition-colors" />
                    <x-filament::icon x-show="copied" x-cloak icon="heroicon-o-check-badge" class="w-4 h-4 text-success-500" />

                    <span x-show="copied" x-cloak x-transition class="absolute -top-8 right-0 bg-success-600 text-white text-[8px] px-2 py-0.5 rounded font-black uppercase shadow-xl border border-white/10">
                        Copied!
                    </span>
                </div>
            </div>
        </div>

        {{-- System Footer --}}
        <div class="pt-4 border-t border-white/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded bg-white/10 border border-white/5">
                        <x-filament::icon icon="heroicon-m-finger-print" class="w-3.5 h-3.5 text-gray-400" />
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[8px] font-black text-gray-500 uppercase tracking-tighter">OS Kernel</span>
                        <span class="text-[10px] font-mono font-bold text-gray-300 leading-none">
                            {{ Str::limit($os['kernel'], 14) }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col items-end">
                    <span class="text-[8px] font-black text-primary-500 uppercase tracking-widest">{{ $stats['database']['engine'] }} Engine</span>
                    <span class="text-[10px] font-mono font-bold text-gray-500 leading-none">v{{ $stats['database']['version'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
