<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use App\Services\PageService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class PageIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $deleteId = null;
    public $showDeleteModal = false;

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->deleteId = null;
        $this->showDeleteModal = false;
    }

    public function delete(PageService $service)
    {
        if ($this->deleteId) {
            $page = Page::find($this->deleteId);
            if ($page) {
                $service->delete($page);
                $this->dispatch('toast', message: 'Halaman berhasil dihapus', type: 'success');
            } else {
                $this->dispatch('toast', message: 'Halaman tidak ditemukan', type: 'error');
            }
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function updateStatus($id, $status)
    {
        $page = Page::find($id);
        if ($page) {
            $page->update(['is_active' => $status]);
            $this->dispatch('toast', message: 'Status halaman diperbarui', type: 'success');
        }
    }

    public function render()
    {
        $pages = Page::query()
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.pages.page-index', [
            'pages' => $pages,
        ]);
    }
}
