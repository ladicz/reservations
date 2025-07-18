<?php

namespace App\Livewire;

use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Authentication extends Component
{
    public $email;
    public $password;

    protected function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ];
    }

    /**
     * Zpracovani formulare prihlaseni
     */
    public function login()
    {
        $this->validate();
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->flash('success', __('Přihlášení úspěšné'));
            return $this->redirectIntended();
        } else {
            session()->flash('fail', __('Nesprávný email nebo heslo'));
        }

    }

    public function render()
    {
        return view('livewire.authentication');
    }
}
