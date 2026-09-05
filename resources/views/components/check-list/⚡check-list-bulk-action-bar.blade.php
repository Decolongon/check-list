<?php

use Livewire\Component;

new class extends Component
{
    public array $selected = [];
};
?>

<div>
    <div class="sticky top-2 z-10 flex flex-col gap-2 rounded-xl border border-zinc-900 bg-zinc-900 p-3 shadow-lg sm:flex-row sm:items-center sm:justify-between sm:p-3 dark:border-white dark:bg-white">
        <div class="flex items-center gap-2.5">
            <div class="flex size-7 items-center justify-center rounded-full bg-white text-zinc-900 dark:bg-zinc-900 dark:text-white">
                <flux:icon.check class="size-4" stroke-width="3" />
            </div>
            <p class="text-sm font-medium text-white dark:text-zinc-900">
                {{ __(':count selected', ['count' => count($selected)]) }}
                <span class="hidden font-normal opacity-80 sm:inline">— {{ __('will be marked as finished') }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2">{{ $slot }}</div>
    </div>
</div>