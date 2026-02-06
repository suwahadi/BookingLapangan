<?php

namespace App\Livewire\Admin\System;

use App\Models\AuditLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Audit Logs - Admin Panel')]
class AuditLogIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $action = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedAction()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AuditLog::query()
            ->with(['actor'])
            ->when($this->search, function($q) {
                $term = "%{$this->search}%";
                $q->whereHas('actor', fn($q2) => $q2->where('name', 'like', $term))
                  ->orWhere('action', 'like', $term)
                  ->orWhere('auditable_type', 'like', $term);
            })
            ->when($this->action, fn($q) => $q->where('action', $this->action))
            ->latest();

        $actions = AuditLog::select('action')->distinct()->pluck('action');

        return view('livewire.admin.system.audit-log-index', [
            'logs' => $query->paginate(30),
            'actions' => $actions,
        ]);
    }
}
