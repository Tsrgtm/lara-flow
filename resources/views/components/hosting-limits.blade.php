@php
    $account = $getRecord()->hostingAccount;
@endphp

<div>
    @if(!$account)
        <span class="text-xs text-gray-400 dark:text-gray-500 italic">No Account</span>
    @elseif($account->is_suspended)
        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
            Suspended
        </span>
    @else
        <div class="grid grid-cols-2 gap-x-3 gap-y-1 text-[11px] leading-tight max-w-[200px]">
            {{-- Disk --}}
            <div class="flex items-center gap-1.5" title="Disk Usage">
                <x-heroicon-m-server class="w-3.5 h-3.5 text-gray-400" />
                <span class="font-medium dark:text-gray-300">{{ round($account->disk_limit_mb / 1024, 1) }}GB</span>
            </div>

            {{-- Bandwidth --}}
            <div class="flex items-center gap-1.5" title="Bandwidth">
                <x-heroicon-m-arrow-path class="w-3.5 h-3.5 text-gray-400" />
                <span class="font-medium dark:text-gray-300">{{ round($account->bandwidth_limit_mb / 1024, 1) }}GB</span>
            </div>

            {{-- Databases --}}
            <div class="flex items-center gap-1.5" title="Databases">
                <x-heroicon-m-circle-stack class="w-3.5 h-3.5 text-gray-400" />
                <span class="dark:text-gray-400">DBs: {{ $account->database_limit ?? 0 }}</span>
            </div>

            {{-- Emails --}}
            <div class="flex items-center gap-1.5" title="Email Accounts">
                <x-heroicon-m-envelope class="w-3.5 h-3.5 text-gray-400" />
                <span class="dark:text-gray-400">Mail: {{ $account->email_limit ?? 0 }}</span>
            </div>
        </div>
    @endif
</div>
