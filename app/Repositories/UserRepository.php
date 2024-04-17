<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;

class UserRepository{

    protected $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function getUserByEmail($data){
        $result = $this->user->where('email', $data)->first();

        return $result;
    }

    public function save($data){
        $result = $this->user->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password']
        ]);

        return $result;
    }

    public function updatePassword($data){
        $user = $this->user->find($data->id);
        $result = $user->update([
            'password' => $data['password']
        ]);

        return $result;
    }

    public function updateEmailVerified($data){
        $user = $this->user->where('email', $data);
        $result = $user->update([
            'email_verified_at' => Carbon::now()
        ]);

        return $result;
    }
}
