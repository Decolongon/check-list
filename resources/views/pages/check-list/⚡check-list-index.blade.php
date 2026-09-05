<?php

use App\Models\Category;
use App\Services\ChecklistService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public Category $category;

    public ?string $name = null;

    public array $selected = [];

    protected $checkListService;

    #[Locked]
    public int $perPage = 5;

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->perPage += 5;
        unset($this->getCheckLists);
        unset($this->hasMore);
    }

    public function boot(ChecklistService $checkListService)
    {
        $this->checkListService = $checkListService;
    }

    #[Computed]
    public function getCheckLists(): Collection
    {
        return $this->checkListService->getCheckLists($this->category, $this->perPage);
    }

    #[Computed]
    public function totalCount(): int
    {
        return $this->checkListService->totalCount($this->category);
    }

    #[Computed]
    public function hasMore(): bool
    {
        return $this->checkListService->hasMore($this->category, $this->perPage);
    }

    #[Computed]
    public function stats(): array
    {
        return $this->checkListService->stats($this->category);
    }

    public function toFinish(): void
    {
        $this->checkListService->markSelectedAsFinished($this->category, $this->selected);

        $this->clearSelection();
        unset($this->getCheckLists);
        unset($this->stats);
        unset($this->totalCount);
        unset($this->hasMore);
    }

    public function toggleSelectAll(): void
    {
        $pendingIds = $this->getCheckLists->where('is_finished', false)->pluck('id')->all();

        // if all pending already selected -> clear, else select all pending
        if (count($pendingIds) > 0 && count(array_intersect($pendingIds, $this->selected)) === count($pendingIds)) {
            $this->selected = array_values(array_diff($this->selected, $pendingIds));
        } else {
            $this->selected = array_values(array_unique(array_merge($this->selected, $pendingIds)));
        }
    }

    public function clearSelection(): void
    {
        $this->reset('selected');
    }

    public function reorder(int|string $id, int $position): void
    {
        $this->checkListService->reorder($this->category, $id, $position);
        unset($this->getCheckLists);
    }

    public function submit(): void
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                Rule::unique('check_lists')->where(function ($query) {
                    return $query->where('author_id', Auth::id())
                        ->where('category_id', $this->category->id);
                }), ],
        ]);

        // Insert at top (position 0) and shift existing.
        $this->checkListService->baseIncrement($this->category);

        Auth::user()->checklists()->create([
            'category_id' => $this->category->id,
            'name' => $this->name,
            'position' => 0,
        ]);

        $this->reset('name');
        unset($this->getCheckLists);
        unset($this->stats);
        unset($this->totalCount);
        unset($this->hasMore);
    }

    public function toggle(int $id): void
    {
        $this->checkListService->toggleFinish($this->category, $id);
        unset($this->getCheckLists);
        unset($this->stats);
    }

    public function delete(int $id): void
    {
        $this->checkListService->delete($this->category, $id);
        unset($this->getCheckLists);
        unset($this->stats);
        unset($this->totalCount);
        unset($this->hasMore);
    }

    public function clear(): void
    {
        $this->checkListService->clearAll($this->category);
        $this->reset('selected');
        $this->perPage = 5;
        unset($this->getCheckLists);
        unset($this->stats);
        unset($this->totalCount);
        unset($this->hasMore);
    }
};
?>

<div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
    {{-- Breadcrumb / Header --}}
    <div class="flex flex-col gap-3">
        <a
            href="{{ route('categories') }}"
            wire:navigate
            class="inline-flex w-fit items-center gap-1.5 text-sm text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
        >
            <flux:icon.chevron-left class="size-4 shrink-0" />
            {{ __('Back to Categories') }}
        </a>

        <div class="flex items-start gap-3 sm:gap-4">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-900 text-white sm:size-12 dark:bg-white dark:text-zinc-900">
                <flux:icon.clipboard-document-list class="size-5 sm:size-6" />
            </div>
            <div class="min-w-0 flex-1">
                <flux:heading
                    size="xl"
                    level="1"
                    class="truncate text-xl sm:text-2xl"
                >{{ $category->category_name }}</flux:heading>
                <flux:text variant="subtle" class="mt-1 truncate text-sm"
                    >/{{ $category->category_slug }} &middot; {{ $this->stats['total'] }} {{ Str::plural('item', $this->stats['total']) }}</flux:text>
            </div>
            <flux:badge color="zinc" size="sm" class="hidden shrink-0 sm:inline-flex">{{ __('Checklist') }}</flux:badge>
        </div>
    </div>

    {{-- Stats / Progress --}}
    @if ($this->stats['total'] > 0)
        <livewire:check-list.check-list-stats :stats="$this->stats" />
    @endif

    {{-- Add form --}}
    <flux:card class="flex flex-col gap-3 sm:gap-4">
        <div class="flex items-center gap-2">
            <flux:icon.plus class="size-4 text-zinc-500" />
            <flux:heading size="sm">{{ __('Add new item') }}</flux:heading>
        </div>
        <form wire:submit.prevent="submit" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:gap-3">
            <div class="min-w-0 flex-1">
                <flux:input
                    wire:model="name"
                    :label="__('Checklist name')"
                    placeholder="{{ __('e.g. Buy milk, Call client, Finish report') }}"
                    autocomplete="off"
                />
            </div>
            <flux:button type="submit" variant="primary" icon="plus" class="w-full shrink-0 sm:w-auto">
                {{ __('Add') }}
            </flux:button>
        </form>
    </flux:card>

    {{-- Checklist items + Bulk toFinish --}}
    <div class="flex flex-col gap-3">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <flux:heading size="sm" class="flex items-center gap-2">
                <flux:icon.list-bullet class="size-4 text-zinc-400" />
                {{ __('Your checklists') }}
                @if ($this->stats['total'] > 0)
                    <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 tabular-nums dark:bg-zinc-800 dark:text-zinc-300">{{ $this->stats['total'] }}</span>
                @endif
            </flux:heading>

            @if ($this->stats['total'] > 0)
                <div class="flex flex-wrap items-center gap-2">
                    <flux:text
                        class="hidden text-xs sm:block"
                        variant="subtle"
                    >{{ __('Tap checkbox to finish instantly') }}</flux:text>
                    @if ($this->stats['remaining'] > 0)
                        <flux:button variant="ghost" size="sm" wire:click="toggleSelectAll" class="h-7 px-2 text-xs">
                            @if (count(array_intersect($this->getCheckLists->where('is_finished', false)->pluck('id')->all(), $selected)) === $this->stats['remaining'] && $this->stats['remaining'] > 0)
                                {{ __('Clear all') }}
                            @else
                                {{ __('Select all') }} ({{ $this->stats['remaining'] }})
                            @endif
                        </flux:button>
                    @endif
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        wire:click="clear"
                        wire:confirm="{{ __('Clear all :count items in this category? This cannot be undone.', ['count' => $this->totalCount]) }}"
                        class="h-7 px-2 text-xs text-zinc-500 hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400"
                        title="{{ __('Clear all items') }}"
                    >
                        {{ __('Clear') }}
                    </flux:button>
                </div>
            @endif
        </div>

        {{-- Bulk action bar - wired to toFinish() --}}
        @if (count($selected) > 0)
            <livewire:check-list.check-list-bulk-action-bar :selected="$selected">
                <flux:button
                    variant="ghost"
                    size="sm"
                    wire:click="clearSelection"
                    class="w-full !bg-white/10 !text-white hover:!bg-white/20 sm:w-auto dark:!bg-zinc-900/10 dark:!text-zinc-900 dark:hover:!bg-zinc-900/20"
                >
                    {{ __('Clear') }}
                </flux:button>
                <flux:button
                    variant="primary"
                    size="sm"
                    wire:click="toFinish"
                    icon="check-circle"
                    class="w-full bg-white !text-zinc-900 hover:bg-zinc-100 sm:w-auto dark:bg-zinc-900 dark:!text-white dark:hover:bg-zinc-800"
                >
                    {{ __('Mark as finished') }}
                </flux:button>
                </livewire:check-list-bulk-action-bar>
        @endif

        <div wire:sort="reorder" class="flex flex-col gap-2.5 sm:gap-3">
            @forelse ($this->getCheckLists as $checkList)
                @php $isSelected = in_array($checkList->id, $selected); @endphp
                {{-- Nested component: child is dumb/presentational, all actions live in parent via slots (Livewire docs: slots) --}}
                <livewire:check-list.check-list-item
                    :checkList="$checkList"
                    :isSelected="$isSelected"
                    :wire:key="'checklist-'.$checkList->id.'-'.$checkList->is_finished.'-'.$isSelected"
                    wire:sort:item="{{ $checkList->id }}"
                >
                    <livewire:slot name="checkbox">
                        @if ($checkList->is_finished)
                            <label
                                class="relative -my-1 -ml-1 flex size-11 shrink-0 cursor-pointer items-center justify-center sm:size-12"
                                title="{{ __('Click to mark as pending') }}"
                            >
                                <input
                                    type="checkbox"
                                    checked
                                    wire:click="toggle({{ $checkList->id }})"
                                    aria-label="{{ __('Uncheck :name', ['name' => $checkList->name]) }}"
                                    class="peer size-6 shrink-0 cursor-pointer appearance-none rounded-md border-2 border-zinc-900 bg-white bg-zinc-900 transition-all focus-visible:ring-2 focus-visible:ring-zinc-900 focus-visible:ring-offset-2 focus-visible:outline-none sm:size-[26px] dark:border-white dark:bg-white dark:focus-visible:ring-white dark:focus-visible:ring-offset-zinc-900"
                                />
                                <flux:icon.check
                                    class="pointer-events-none absolute size-3.5 text-white sm:size-4 dark:text-zinc-900"
                                    stroke-width="3"
                                />
                            </label>
                        @else
                            <label
                                class="relative -my-1 -ml-1 flex size-11 shrink-0 cursor-pointer items-center justify-center sm:size-12"
                                title="{{ __('Click to mark as finished') }}"
                            >
                                <input
                                    type="checkbox"
                                    wire:click="toggle({{ $checkList->id }})"
                                    @checked(false)
                                    aria-label="{{ __('Check :name', ['name' => $checkList->name]) }}"
                                    class="peer size-6 shrink-0 cursor-pointer appearance-none rounded-md border-2 border-zinc-300 bg-white transition-all checked:border-zinc-900 checked:bg-zinc-900 focus-visible:ring-2 focus-visible:ring-zinc-900 focus-visible:ring-offset-2 focus-visible:outline-none sm:size-[26px] dark:border-zinc-600 dark:bg-zinc-800 dark:checked:border-white dark:checked:bg-white dark:focus-visible:ring-white dark:focus-visible:ring-offset-zinc-900"
                                />
                                <flux:icon.check
                                    class="pointer-events-none absolute size-3.5 scale-0 text-white opacity-0 transition-all peer-checked:scale-100 peer-checked:opacity-100 sm:size-4 dark:text-zinc-900"
                                    stroke-width="3"
                                />
                            </label>
                        @endif
                    </livewire:slot>
                    <livewire:slot name="actions">
                        <flux:button
                            wire:click="toggle({{ $checkList->id }})"
                            variant="ghost"
                            size="sm"
                            :icon="$checkList->is_finished ? 'arrow-path' : 'check'"
                            class="size-8 p-0 sm:size-9"
                            :aria-label="$checkList->is_finished ? __('Mark as pending') : __('Mark as done')"
                            :title="$checkList->is_finished ? __('Mark as pending') : __('Quick finish')"
                        />
                        <flux:button
                            wire:click="delete({{ $checkList->id }})"
                            wire:confirm="{{ __('Delete this item? This cannot be undone.') }}"
                            variant="ghost"
                            size="sm"
                            icon="trash"
                            class="size-8 p-0 text-zinc-400 hover:text-red-600 sm:size-9 dark:text-zinc-500 dark:hover:text-red-400"
                            aria-label="{{ __('Delete') }}"
                        />
                    </livewire:slot>
                    </livewire:check-list-item>
            @empty
                <flux:card class="flex flex-col items-center justify-center gap-3 border-dashed py-10 text-center sm:py-14">
                    <div class="flex size-12 items-center justify-center rounded-full bg-zinc-100 sm:size-14 dark:bg-zinc-800">
                        <flux:icon.clipboard-document-list class="size-6 text-zinc-400 sm:size-7" />
                    </div>
                    <flux:heading size="sm">{{ __('No checklists yet') }}</flux:heading>
                    <flux:text variant="subtle" class="max-w-sm text-sm">
                        {{ __('Add your first item above. Use the checkbox beside each name to select, then tap Mark as finished to bulk complete.') }}
                    </flux:text>
                    <div class="mt-1 flex items-center gap-1.5 rounded-full border border-dashed border-zinc-200 bg-zinc-50 px-3 py-1.5 text-xs text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                        <span class="size-3 rounded border-2 border-zinc-300 dark:border-zinc-600"></span>
                        {{ __('Example: Grocery shopping') }}
                        <span class="ml-2 hidden text-zinc-400 sm:inline">→</span>
                        <span class="hidden items-center gap-1 sm:inline-flex"><span class="size-3 rounded bg-zinc-900 dark:bg-white"></span>
                            {{ __('Selected') }} → {{ __('Mark as finished') }}</span>
                    </div>
                </flux:card>
            @endforelse
        </div>

        {{-- Infinite scroll sentinel / end state --}}
        @if ($this->hasMore)
            <div
                wire:intersect.half="loadMore"
                class="flex flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-zinc-200 bg-white py-6 dark:border-zinc-700 dark:bg-zinc-900"
            >
                <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                    <svg class="size-4 animate-spin text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Loading more…') }}
                </div>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Showing :shown of :total', ['shown' => $this->getCheckLists->count(), 'total' => $this->totalCount]) }}
                </p>
            </div>
        @elseif ($this->totalCount > 0)
            <div class="flex items-center justify-center gap-2 rounded-xl bg-zinc-50 py-4 dark:bg-zinc-800/50">
                <flux:icon.check-circle class="size-4 text-emerald-500" />
                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-300">{{ __('All :count items loaded', ['count' => $this->totalCount]) }}</span>
                <span class="hidden text-xs text-zinc-400 sm:inline dark:text-zinc-500">— {{ __('You’re all caught up') }}</span>
            </div>
        @endif

        <flux:text class="px-1 text-center text-xs sm:text-left" variant="subtle">
            @if (count($selected) > 0)
                {{ __(':count pending selected. Tap Mark as finished above.', ['count' => count($selected)]) }}
            @else
                <span class="hidden sm:inline">{{ __('Tip: tap checkbox to instantly finish. Use Select all → Mark as finished for bulk (toFinish).') }}</span>
                <span class="sm:hidden">{{ __('Tap checkbox to finish instantly. Select all for bulk.') }}</span>
            @endif
        </flux:text>
    </div>
</div>
