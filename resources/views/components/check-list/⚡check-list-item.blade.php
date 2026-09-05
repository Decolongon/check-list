<?php

use App\Models\CheckList;
use Livewire\Attributes\Reactive;
use Livewire\Component;

new class extends Component
{
    public CheckList $checkList;

    #[Reactive]
    public bool $isSelected = false;
};
?>

<div
    wire:key="checklist-{{ $checkList->id }}-{{ $checkList->is_finished }}"
    class="group flex items-center gap-3 rounded-xl border bg-white p-3 shadow-sm transition-all hover:shadow-md sm:gap-4 sm:p-4
    {{ $checkList->is_finished ? 'border-zinc-200 bg-zinc-50/70 dark:border-zinc-800 dark:bg-zinc-900/50' : ($isSelected ? 'border-zinc-900 bg-zinc-50 dark:border-white dark:bg-zinc-800 ring-1 ring-zinc-900 dark:ring-white' : 'border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900') }}"
>
    {{-- Drag handle for wire:sort --}}
    <div wire:sort:item="{{ $checkList->id }}" class="flex shrink-0 cursor-grab items-center justify-center rounded p-1 text-zinc-300 hover:text-zinc-500 active:cursor-grabbing dark:text-zinc-500 dark:hover:text-zinc-300" title="{{ __('Drag to reorder') }}">
        <flux:icon.bars-3 class="size-4" />
    </div>

    {{-- Leading checkbox - supplied via slot so action lives in parent (Livewire docs: slots) --}}
    {{ $slots['checkbox'] }}

    {{-- Fallback default slot --}}
    {{-- {{ $slot }} --}}

    {{-- Name + meta - child just reacts to $checkList / $isSelected props from parent --}}
    <div class="min-w-0 flex-1">
        <p class="break-words text-[15px] font-medium leading-snug sm:text-[15px] {{ $checkList->is_finished ? 'text-zinc-500 line-through decoration-zinc-400 dark:text-zinc-400' : ($isSelected ? 'text-zinc-900 dark:text-white' : 'text-zinc-900 dark:text-white') }}">
            {{ $checkList->name }}
        </p>
        <div class="mt-0.5 flex flex-wrap items-center gap-1.5 sm:gap-2">
            <span class="inline-flex items-center gap-1 text-[11px] sm:text-xs {{ $checkList->is_finished ? 'text-emerald-600 dark:text-emerald-400' : ($isSelected ? 'text-zinc-900 dark:text-zinc-200' : 'text-zinc-500 dark:text-zinc-400') }}">
                @if ($checkList->is_finished)
                    <flux:icon.check-circle class="size-3 sm:size-3.5" />
                    {{ __('Completed') }}
                @elseif ($isSelected)
                    <flux:icon.check-circle class="size-3 sm:size-3.5" />
                    {{ __('Selected') }}
                @else
                    <flux:icon.clock class="size-3 sm:size-3.5" />
                    {{ __('Pending') }}
                @endif
            </span>
            <span class="hidden text-zinc-300 sm:inline dark:text-zinc-600">&middot;</span>
            <span class="text-[11px] text-zinc-500 sm:text-xs dark:text-zinc-400">{{ $checkList->created_at->format('d M Y') }}</span>
        </div>
    </div>

    {{-- Actions slot - supplied via slot so toggle/delete stay in parent --}}
    <div class="flex shrink-0 items-center gap-1 sm:gap-1.5">{{ $slots['actions'] ?? '' }}</div>
</div>
