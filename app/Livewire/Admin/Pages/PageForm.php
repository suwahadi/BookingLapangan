<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use App\Services\PageService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Str;

#[Layout('layouts.admin')]
class PageForm extends Component
{
    public $pageId = null;
    public $title = '';
    public $slug = '';
    public $content_html = '';
    public $is_active = true;
    public $meta_description = '';
    public $meta_keywords = '';
    
    // Flag to track if slug was manually edited
    public $isSlugManuallyEdited = false;

    public function mount($page = null)
    {
        if ($page) {
            // Livewire automatically resolves the route model binding if variable matches route param
            // Assuming route is {page} and we accept it here. 
            // If passed as model:
            if ($page instanceof Page) {
                 $this->pageId = $page->id;
                 $this->title = $page->title;
                 $this->slug = $page->slug;
                 $this->content_html = $page->content_html;
                 $this->is_active = $page->is_active;
                 $this->meta_description = $page->meta_description;
                 $this->meta_keywords = $page->meta_keywords;
                 $this->isSlugManuallyEdited = true;
            } 
            // If passed as ID (legacy or direct call)
            elseif (is_numeric($page)) {
                $model = Page::find($page);
                if($model) {
                    $this->pageId = $model->id;
                    $this->title = $model->title;
                    $this->slug = $model->slug;
                    $this->content_html = $model->content_html;
                    $this->is_active = $model->is_active;
                    $this->meta_description = $model->meta_description;
                    $this->meta_keywords = $model->meta_keywords;
                    $this->isSlugManuallyEdited = true;
                }
            }
        }
    }

    public function updatedTitle($value)
    {
        if (!$this->isSlugManuallyEdited) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedSlug()
    {
        $this->isSlugManuallyEdited = true;
        // Basic slug sanitization
        $this->slug = Str::slug($this->slug);
    }

    public function save(PageService $service)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug' . ($this->pageId ? ',' . $this->pageId : ''),
            'content_html' => 'required',
            'is_active' => 'boolean',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
        ];

        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'content_html' => $this->content_html,
            'is_active' => $this->is_active,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
        ];

        if ($this->pageId) {
            $page = Page::findOrFail($this->pageId);
            $service->update($page, $data);
            $message = 'Halaman berhasil diperbarui';
        } else {
            $service->create($data);
            $message = 'Halaman berhasil dibuat';
        }

        session()->flash('success', $message);
        return redirect()->route('admin.pages.index');
    }

    public function render()
    {
        return view('livewire.admin.pages.page-form');
    }
}
