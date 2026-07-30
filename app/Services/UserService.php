<?php

namespace App\Services;

use App\Exceptions\UserEmailExistsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Function creates new user according to input data $data.
     *
     * @param array $data User data
     * @return User Created user instance
     * @throws UserEmailExistsException in case email uniqueness check fails
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
