<?php

use Livewire\Attributes\Reactive;
use Livewire\Component;

new class extends Component
{
    #[Reactive]
    public array $stats;
};
?>

<div>
    <div class="grid grid-cols-3 gap-2 sm:gap-3">
        <div class="rounded-xl border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total') }}</p>
            <p class="mt-1 text-xl font-semibold text-zinc-900 sm:text-2xl dark:text-white">
                {{ $this->stats['total'] }}
            </p>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 sm:p-4 dark:border-emerald-900/50 dark:bg-emerald-950/30">
            <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400">{{ __('Done') }}</p>
            <p class="mt-1 text-xl font-semibold text-emerald-700 sm:text-2xl dark:text-emerald-300">
                {{ $this->stats['done'] }}
            </p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 sm:p-4 dark:border-amber-900/50 dark:bg-amber-950/30">
            <p class="text-xs font-medium text-amber-700 dark:text-amber-400">{{ __('Remaining') }}</p>
            <p class="mt-1 text-xl font-semibold text-amber-700 sm:text-2xl dark:text-amber-300">
                {{ $this->stats['remaining'] }}
            </p>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
            <div
                class="h-full rounded-full bg-zinc-900 transition-all duration-500 dark:bg-white"
                style="width: {{ $this->stats['percent'] }}%"
            ></div>
        </div>
        <span class="shrink-0 text-sm font-medium text-zinc-700 tabular-nums dark:text-zinc-300">{{ $this->stats['percent'] }}%</span>
    </div>
</div>
