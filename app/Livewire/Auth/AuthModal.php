<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AuthModal extends Component
{
    // Modal state
    public bool $showModal = false;
    public string $mode = 'login'; // 'login' or 'register'

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $remember = false;

    protected $listeners = ['openAuthModal' => 'openModal'];

    public function openModal($mode = 'login')
    {
        $this->resetForm();
        $this->mode = $mode;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function switchMode($mode)
    {
        $this->resetForm();
        $this->mode = $mode;
    }

    public function resetForm()
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'remember']);
        $this->resetValidation();
    }

    // Login
    public function login()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            $this->closeModal();
            
            session()->flash('success', 'Selamat datang kembali!');
            
            return $this->redirect(request()->header('Referer', '/'), navigate: true);
        }

        $this->addError('email', 'Email atau password salah.');
    }

    // Register
    public function register()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        Auth::login($user);
        session()->regenerate();
        
        $this->closeModal();
        
        session()->flash('success', 'Akun berhasil dibuat! Selamat datang.');
        
        return $this->redirect(request()->header('Referer', '/'), navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.auth-modal');
    }
}
