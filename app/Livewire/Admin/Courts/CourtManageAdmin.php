<?php

namespace App\Livewire\Admin\Courts;

use App\Models\Venue;
use App\Models\VenueCourt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Kelola Lapangan - Admin Panel')]
class CourtManageAdmin extends Component
{
    public Venue $venue;
    
    // Modal State
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?VenueCourt $editingCourt = null;

    // Form fields
    public string $name = '';
    public string $sport = '';
    public string $floor_type = '';
    public bool $is_active = true;

    public function mount(Venue $venue)
    {
        $this->venue = $venue;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit(VenueCourt $court)
    {
        $this->isEdit = true;
        $this->editingCourt = $court;
        $this->name = $court->name;
        $this->sport = $court->sport;
        $this->floor_type = $court->floor_type ?? '';
        $this->is_active = $court->is_active;
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->name = '';
        $this->sport = '';
        $this->floor_type = '';
        $this->is_active = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'sport' => 'required|string|max:100',
            'floor_type' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        if ($this->isEdit) {
            $this->editingCourt->update([
                'name' => $this->name,
                'sport' => $this->sport,
                'floor_type' => $this->floor_type,
                'is_active' => $this->is_active,
            ]);
            $this->dispatch('toast', message: 'Lapangan berhasil diperbarui!', type: 'success');
        } else {
            $this->venue->courts()->create([
                'name' => $this->name,
                'sport' => $this->sport,
                'floor_type' => $this->floor_type,
                'is_active' => $this->is_active,
            ]);
            $this->dispatch('toast', message: 'Lapangan baru berhasil ditambahkan!', type: 'success');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleStatus(VenueCourt $court)
    {
        $court->update(['is_active' => !$court->is_active]);
        $this->dispatch('toast', message: 'Status lapangan diperbarui.', type: 'info');
    }

    public function render()
    {
        return view('livewire.admin.courts.court-manage-admin', [
            'courts' => $this->venue->courts()->latest()->get(),
        ]);
    }
}
