<?php

use App\Models\Category;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public Category $category;
};
?>

<div>
    <a
        href="{{ route('checklists', ['category' => $category->category_slug, 'user' => $category->author->name]) }}"
        wire:navigate
        class="block"
    >
        <flux:card
            wire:key="category-{{ $category->id }}"
            class="group relative flex cursor-pointer flex-col gap-3 transition-all hover:border-zinc-300 hover:shadow-md dark:hover:border-zinc-600"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-zinc-100 transition-colors group-hover:bg-zinc-900 group-hover:text-white dark:bg-zinc-800 dark:group-hover:bg-white dark:group-hover:text-zinc-900">
                    <flux:icon.folder class="size-5" />
                </div>
                <flux:badge size="sm" color="zinc" inset="top bottom">{{ __('Category') }}</flux:badge>
            </div>

            <div class="flex flex-col gap-1">
                <flux:heading
                    size="sm"
                    class="truncate"
                    :title="$category->category_name"
                >{{ $category->category_name }}</flux:heading>
                <flux:text class="truncate text-xs" variant="subtle">/{{ $category->category_slug }}</flux:text>
            </div>

            @if (isset($category->created_at))
                <flux:text class="mt-1 text-xs" variant="subtle">
                    {{ __('Created :date', ['date' => $category->created_at->format('M d, Y')]) }}
                </flux:text>
            @endif
        </flux:card>
    </a>
</div>
