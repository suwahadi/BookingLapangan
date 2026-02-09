<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Logout extends Component
{
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        session()->flash('success', 'Berhasil keluar. Sampai jumpa lagi!');
        return $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return <<<'HTML'
            <button wire:click="logout" class="w-full text-left block px-4 py-2 text-sm font-bold text-rose-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors tracking-tight">
                Keluar
            </button>
        HTML;
    }
}
