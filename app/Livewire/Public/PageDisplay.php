<?php

namespace App\Livewire\Public;

use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PageDisplay extends Component
{
    public $page;

    public function mount(Page $page)
    {
        if (! $page->is_active) {
            abort(404);
        }

        $this->page = $page;
    }

    public function render()
    {
        return view('livewire.public.page-display')
            ->title($this->page->title . ' - ' . config('app.name'));
    }
}
