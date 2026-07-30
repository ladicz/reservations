<?php

namespace App\Livewire;

use App\Services\UserService;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateUser extends Component
{
    #[Validate('required|string')]
    public string $name;

    #[Validate('required|email|unique:users,email')]
    public string $email;

    #[Validate('required|confirmed|min:5')]
    public string $password;
    public string $password_confirmation;

    /**
     * Form action to create new user
     *
     * @param UserService $service
     * @return void
     */
    public function save(UserService $service): void
    {
        $service->createUser($this->validate());

        session()->flash('success', __('User created successfully'));
        $this->redirect('/');
    }

    /**
     * Render the component view
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.create-user');
    }
}
