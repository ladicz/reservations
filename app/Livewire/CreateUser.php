<?php

namespace App\Livewire;

use App\Services\UserService;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateUser extends Component
{
    protected function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:5',
        ];
    }

    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    /**
     * Zpracovani formulare vytvoreni novehu uzivatele
     */
    public function save(UserService $service)
    {
        $service->createUser($this->validate());

        session()->flash('success', __('Uživatel vytvořen'));
        return $this->redirect('/');
    }

    public function render()
    {
        return view('livewire.create-user');
    }
}
