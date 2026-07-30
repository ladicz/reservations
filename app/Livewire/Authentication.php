<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Authentication extends Component
{
    #[Validate('required|email|exists:users,email')]
    public string $email;

    #[Validate('required')]
    public string $password;

    /**
     * Login form action
     *
     * @return void
     */
    public function login(): void
    {
        $this->validate();

        $key = 'login-attempts:' . strtolower($this->email) . '|' . request()->ip();

        // restriction of login attempts
        if (RateLimiter::tooManyAttempts($key, 5))
        {
            session()->flash('fail', __('Too many login attempts. Please try again later.'));
            return;
        }

        RateLimiter::hit($key, 60); // 60 sekund blokace

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password]))
        {
            RateLimiter::clear($key);
            session()->flash('success', __('Login successful'));
            $this->redirectIntended();
        } else {
            session()->flash('fail', __('Login failed'));
        }
    }

    /**
     * Render the component view
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.authentication');
    }
}
