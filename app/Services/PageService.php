<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Str;

class PageService
{
    public function create(array $data): Page
    {
        $data['slug'] = $this->generateSlug($data['slug'] ?? $data['title']);
        return Page::create($data);
    }

    public function update(Page $page, array $data): Page
    {
        if (isset($data['slug']) && $data['slug'] !== $page->slug) {
            $data['slug'] = $this->generateSlug($data['slug'], $page->id);
        } elseif (isset($data['title']) && (!isset($data['slug']) || empty($data['slug']))) {
             // Re-generate slug if title changed and slug is empty
             $data['slug'] = $this->generateSlug($data['title'], $page->id);
        }

        $page->update($data);
        return $page;
    }

    public function delete(Page $page): void
    {
        $page->delete();
    }

    public function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Page::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $count++;
            $slug = "{$originalSlug}-{$count}";
        }

        return $slug;
    }
}
