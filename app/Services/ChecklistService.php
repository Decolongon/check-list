<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CheckList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChecklistService
{
    public function baseIncrement(Category $category): void
    {
        $this->baseQuery($category)->increment('position');
    }
    private function baseQuery(Category $category)
    {
        return CheckList::query()->with('author:id,name')->where('category_id', $category->id);
    }

    public function getCheckLists(Category $category, int $page = 5)
    {
        return $this->baseQuery($category)->orderBy('position')->orderByDesc('id')->take($page)->get();
    }

    public function totalCount(Category $category)
    {
        return $this->baseQuery($category)->count();
    }

    public function stats(Category $category): array
    {
        $total = $this->totalCount($category);
        $done = $this->baseQuery($category)->where('is_finished', true)->count();
        $remaining = $total - $done;

        return [
            'total' => $total,
            'done' => $done,
            'remaining' => $remaining,
            'percent' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
        ];
    }

    public function markSelectedAsFinished(Category $category, array $selectedIds): void
    {
        if (empty($selectedIds)) {
            return;
        }

        $this->baseQuery($category)
            ->whereIn('id', $selectedIds)
            ->chunk(100, function ($lists) {
                foreach ($lists as $list) {
                    $list->update(['is_finished' => true]);
                }
            });
    }

    public function hasMore(Category $category, int $perPage): bool
    {
        return $this->totalCount($category) > $this->getChecklists($category, $perPage)->count();
    }

    public function toggleFinish(Category $category,int $id): void
    {
        $checkList = $this->baseQuery($category)->where('id', $id)->firstOrFail();
        abort_if($checkList->author_id !== Auth::id(), 403, 'You are not authorized to update this checklist.');
        $checkList->update(['is_finished' => !$checkList->is_finished]);
    }

    public function delete(Category $category, int $id): void
    {
        $checkList = $this->baseQuery($category)->findOrFail($id);
        abort_if($checkList->author_id !== Auth::id(), 403, 'You are not authorized to delete this checklist.');
        $checkList->delete();
    }

    public function clearAll(Category $category): void
    {
        abort_if($category->author_id !== Auth::id(), 403, 'You are not authorized to clear this category.');
        $this->baseQuery($category)->delete();
    }

    public function reorder(Category $category, int $checklistId, int $newPosition): void
    {
        $newPosition = max(0, $newPosition);

        // Get current ordered IDs (position asc, then id desc)
        $orderedIds = $this->baseQuery($category)
            ->orderBy('position')
            ->orderByDesc('id')
            ->pluck('id')
            ->all();

        if (!in_array($checklistId, $orderedIds, true)) {
            return;
        }

        // Remove the item and re‑insert at the new position
        $orderedIds = array_values(array_diff($orderedIds, [$checklistId]));
        $newPosition = min($newPosition, count($orderedIds));
        array_splice($orderedIds, $newPosition, 0, [$checklistId]);

        DB::transaction(function () use ($category, $orderedIds) {
            foreach ($orderedIds as $pos => $id) {
                $this->baseQuery($category)
                    ->where('id', $id)
                    ->update(['position' => $pos]);
            }
        });
    }
}
