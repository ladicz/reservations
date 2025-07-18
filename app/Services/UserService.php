<?php

namespace App\Services;

use App\Exceptions\UserEmailExistsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Funkce udela kontrolu unikatnosti podle emailu
     * a vyvtori novehu uzivatele na zaklade vstupnich dat poli $data
     */
    public function createUser(array $data): User
    {
        $cnt = User::where('email', $data['email'])->count();

        if($cnt > 0)
            throw new UserEmailExistsException();

        return User::create([
            'password' => Hash::make($data['password']),
            'email' => $data['email'],
            'name' => $data['name'],
        ]);

    }
}
