<?php

namespace App\Livewire\Admin\System;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class UserIndexAdmin extends Component
{
    use WithPagination;

    public string $search = '';

    public ?User $editingUser = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = '';
    
    public bool $isModalOpen = false;

    public function edit(int $userId): void
    {
        if (!auth()->user()->can('user.manage')) {
            session()->flash('error', 'Unauthorized access.');
            return;
        }

        $this->editingUser = User::findOrFail($userId);
        $this->name = $this->editingUser->name;
        $this->email = $this->editingUser->email;
        $this->role = $this->editingUser->roles->first()?->name ?? '';
        $this->password = ''; // Reset password field
        
        $this->isModalOpen = true;
    }

    public function update(): void
    {
        if (!auth()->user()->can('user.manage')) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->editingUser->id,
            'password' => 'nullable|min:8',
            'role' => 'nullable|exists:roles,name',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($this->password);
        }

        $this->editingUser->update($data);

        // Sync Role
        if ($this->role) {
            $this->editingUser->syncRoles([$this->role]);
            // Assume any assigned role from the list implies admin access
            $this->editingUser->update(['is_admin' => true]);
        } else {
            // No role selected = User (Member)
            $this->editingUser->syncRoles([]);
            $this->editingUser->update(['is_admin' => false]);
        }

        session()->flash('success', 'User berhasil diperbarui.');
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->reset(['editingUser', 'name', 'email', 'password', 'role']);
    }

    public function render()
    {
        $users = User::query()
            ->with('roles')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%')->orWhere('email', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.system.user-index-admin', [
            'users' => $users,
            'roles' => \Spatie\Permission\Models\Role::all(),
        ]);
    }
}
