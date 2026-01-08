<x-filament-widgets::widget>
    @php
        $stats = $this->getData();
        $cpu = $stats['cpu'];
        $ram = $stats['memory'];
        $storage = $stats['storage'];
        $db = $stats['database'];
        $network = $stats['network'];
    @endphp

    <div wire:poll.500ms class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- 1. CPU GAUGE --}}
        <div class="p-5 rounded-2xl bg-white dark:bg-gray-950 border border-gray-100 dark:border-white/10 shadow-sm flex flex-col items-center transition-all">
            <div class="flex justify-between w-full items-center mb-3">
                <span class="text-[10px] font-black uppercase text-gray-800 dark:text-gray-200 tracking-widest">Compute</span>
                <div class="p-1.5 rounded-lg {{ $cpu['classes']['light'] }}">
                    <x-heroicon-m-cpu-chip class="w-4 h-4 {{ $cpu['classes']['text'] }}" />
                </div>
            </div>

            <div class="relative flex items-center justify-center py-2">
                <svg class="w-20 h-20 transform -rotate-90">
                    <circle cx="40" cy="40" r="34" stroke="currentColor" stroke-width="5" fill="transparent" class="text-gray-100 dark:text-gray-800/50" />
                    <circle cx="40" cy="40" r="34" stroke="currentColor" stroke-width="5" fill="transparent"
                            stroke-dasharray="213.6" stroke-dashoffset="{{ 213.6 - (213.6 * $cpu['usage']) / 100 }}"
                            class="{{ $cpu['classes']['text'] }} rounded transition-all duration-100" stroke-linecap="round" />
                </svg>
                <div class="absolute flex flex-col items-center justify-center">
                    <span class="text-lg font-black dark:text-white leading-none">{{ $cpu['usage'] }}%</span>
                </div>
            </div>
            <p class="text-[10px] truncate font-mono font-medium text-gray-500 dark:text-gray-400 w-full text-center mt-3 bg-gray-50 dark:bg-white/5 py-1 rounded">
                {{ $cpu['name'] }}
            </p>
        </div>

        {{-- 2. MEMORY --}}
        <div class="p-5 rounded-2xl bg-white dark:bg-gray-950 border border-gray-100 dark:border-white/10 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between w-full items-center">
                <span class="text-[10px] font-black uppercase text-gray-800 dark:text-gray-200 tracking-widest">Memory</span>
                <div class="p-1.5 rounded-lg {{ $ram['classes']['light'] }}">
                    <x-heroicon-m-bolt class="w-4 h-4 {{ $ram['classes']['text'] }}" />
                </div>
            </div>
            <div class="mt-6">
                <div class="flex justify-between items-end mb-2">
                    <span class="text-3xl font-black dark:text-white tracking-tighter">{{ $ram['pct'] }}%</span>
                    <span class="text-[10px] font-mono font-bold text-gray-500 dark:text-gray-400">{{ $ram['used'] }} / {{ $ram['total'] }}</span>
                </div>
                <div class="h-2 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden p-[2px]">
                    <div class="h-full {{ $ram['classes']['bg'] }} rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $ram['pct'] }}%"></div>
                </div>
            </div>
        </div>

        {{-- 3. STORAGE --}}
        <div class="p-5 rounded-2xl bg-white dark:bg-gray-950 border border-gray-100 dark:border-white/10 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between w-full items-center">
                <span class="text-[10px] font-black uppercase text-gray-800 dark:text-gray-200 tracking-widest">Storage</span>
                <div class="p-1.5 rounded-lg {{ $storage['classes']['light'] }}">
                    <x-heroicon-m-folder-open class="w-4 h-4 {{ $storage['classes']['text'] }}" />
                </div>
            </div>
            <div class="mt-6">
                <div class="flex justify-between items-end mb-2">
                    <span class="text-3xl font-black dark:text-white tracking-tighter">{{ $storage['pct'] }}%</span>
                    <span class="text-[10px] font-mono font-bold text-gray-500 dark:text-gray-400">{{ $storage['used'] }} / {{ $storage['total'] }}</span>
                </div>
                <div class="h-2 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden p-[2px]">
                    <div class="h-full {{ $storage['classes']['bg'] }} rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $storage['pct'] }}%"></div>
                </div>
            </div>
        </div>

        {{-- 4. NETWORK & DB --}}
        <div class="p-5 rounded-2xl bg-white dark:bg-gray-950 border border-gray-100 dark:border-white/10 shadow-sm flex flex-col justify-between">
            {{-- Header with Status Pill --}}
            <div class="flex justify-between w-full items-center mb-4">
                <span class="text-[10px] font-black uppercase text-gray-800 dark:text-gray-200 tracking-widest">Network Status</span>
                <div class="flex items-center gap-2 px-2 py-1 rounded-full bg-{{ $network['color'] }}-500/10 border border-{{ $network['color'] }}-500/20">
                    <span class="h-1.5 w-1.5 rounded-full bg-{{ $network['color'] }}-500 animate-pulse"></span>
                    <span class="text-[9px] font-black uppercase text-{{ $network['color'] }}-600 dark:text-{{ $network['color'] }}-400">{{ $network['ping'] }}</span>
                </div>
            </div>

            {{-- Interactive IP Section --}}
            <div class="mb-4" x-data="{
                copyText: '{{ $ipAddress ?? '127.0.0.1' }}',
                copied: false,
                copy() {
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(this.copyText);
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    }
                }
            }">
                <div @click="copy()" class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 group cursor-pointer hover:border-primary-500/50 transition-all duration-300">
                    <div class="flex items-center gap-2 truncate">
                        <x-filament::icon icon="heroicon-m-globe-alt" class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 group-hover:text-primary-500 transition-colors" />
                        <code class="text-[10px] font-mono font-bold text-gray-700 dark:text-gray-300 tracking-tight">{{ $ipAddress ?? '127.0.0.1' }}</code>
                    </div>

                    <div class="flex items-center">
                        <x-filament::icon x-show="!copied" icon="heroicon-o-square-2-stack" class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 group-hover:text-primary-500" />
                        <x-filament::icon x-show="copied" x-cloak icon="heroicon-o-check-circle" class="w-3.5 h-3.5 text-success-500" />
                    </div>
                </div>
            </div>

            {{-- Metadata Stack --}}
            <div class="space-y-3">
                <div class="flex justify-between items-center group">
            <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors">
                {{ $db['engine'] }} Engine
            </span>
                    <span class="text-[10px] font-mono font-bold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-white/5 px-2 py-0.5 rounded border border-gray-200 dark:border-white/5 shadow-sm">
                v{{ $db['version'] }}
            </span>
                </div>

                <div class="flex justify-between items-center group">
                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors">Hostname</span>
                    <span class="text-[10px] font-mono font-bold text-gray-600 dark:text-gray-300 truncate ml-2 tracking-tight">{{ $stats['os']['hostname'] }}</span>
                </div>

                {{-- Footer Kernel Info --}}
                <div class="pt-3 mt-1 border-t border-gray-100 dark:border-white/10">
                    <div class="flex items-center gap-2 opacity-80">
                        <x-filament::icon icon="heroicon-m-finger-print" class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                        <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest truncate">
                    {{ $stats['os']['name'] }} Kernel {{ $stats['os']['kernel'] }}
                </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-filament-widgets::widget>
