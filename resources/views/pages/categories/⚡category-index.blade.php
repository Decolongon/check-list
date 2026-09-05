<?php

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $category_name;

    public function submit(): void
    {
        $validate = $this->validate([
            'category_name' => ['required', 'string', 'max:255', 'min:3', 'unique:categories,category_name'],
        ]);

        $validate['category_slug'] = Str::slug($this->category_name);

        Auth::user()->categories()->create($validate);
        $this->reset('category_name');
    }

    #[Computed]
    public function getCategories(): Collection
    {
        return Category::query()
            ->with('author:id,name')
            ->where('author_id', Auth::user()->id)
            ->get();
    }
};
?>

<div class="flex flex-col gap-6">
    {{-- Header + Create Form --}}
    <div>
        <flux:heading size="xl" level="1">{{ __('Categories') }}</flux:heading>
        <flux:text class="mt-2">{{ __('Organize your checklists by creating categories.') }}</flux:text>
    </div>

    <flux:card class="flex flex-col gap-4">
        <flux:heading>{{ __('Create Category') }}</flux:heading>
        <flux:text variant="subtle" class="text-sm">{{ __('Add a new category to group your checklists.') }}</flux:text>

        <form wire:submit.prevent="submit" class="flex w-full max-w-xl flex-col gap-4 sm:flex-row sm:items-start">
            <div class="flex-1">
                <flux:input
                    wire:model="category_name"
                    :label="__('Category name')"
                    placeholder="{{ __('e.g. Work, Personal, Shopping') }}"
                />
                {{-- <flux:error name="category_name" class="mt-2" /> --}}
            </div>

            <flux:button type="submit" variant="primary" icon="plus" class="mt-6 w-full shrink-0 sm:w-auto">
                {{ __('Add Category') }}
            </flux:button>
        </form>
    </flux:card>

    {{-- Categories Grid --}}

    @island(defer: true)
        @placeholder
            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3">
                @for ($i = 0; $i < 6; $i++)
                    <flux:card class="flex animate-pulse flex-col gap-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="size-10 rounded-lg bg-zinc-200 dark:bg-zinc-700"></div>
                            <div class="h-5 w-16 rounded-full bg-zinc-200 dark:bg-zinc-700"></div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <div class="h-4 w-3/4 rounded bg-zinc-200 dark:bg-zinc-700"></div>
                            <div class="h-3 w-1/2 rounded bg-zinc-100 dark:bg-zinc-800"></div>
                        </div>
                        <div class="h-3 w-24 rounded bg-zinc-100 dark:bg-zinc-800"></div>
                    </flux:card>
                @endfor
            </div>
        @endplaceholder
        <div class="flex justify-end">
            <flux:button wire:click="$refresh" variant="ghost" size="sm" icon="arrow-path">
                {{ __('Refresh') }}
            </flux:button>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3">
            @forelse ($this->getCategories as $category)
                <livewire:categories.category-item :category="$category" :wire:key="'category-'.$category->id" />
            @empty
                <div class="col-span-full">
                    <flux:card class="flex flex-col items-center justify-center gap-3 border-dashed py-12 text-center">
                        <div class="flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <flux:icon.folder-open class="size-6 text-zinc-500 dark:text-zinc-400" />
                        </div>
                        <flux:heading size="sm">{{ __('No categories yet') }}</flux:heading>
                        <flux:text variant="subtle" class="max-w-sm text-sm">
                            {{ __('Get started by creating your first category above. They will appear here as cards.') }}
                        </flux:text>
                    </flux:card>
                </div>
            @endforelse
        </div>
    @endisland
</div>
