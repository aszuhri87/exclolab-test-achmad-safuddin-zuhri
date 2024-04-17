<?php

namespace App\Services;

use App\Repositories\PasswordResetRepository;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use App\Repositories\UserRepository;

class UserService{

    protected $userRepository;
    protected $pwdRepository;


    public function __construct(UserRepository $userRepository, PasswordResetRepository $pwdRepository)
    {
        $this->userRepository = $userRepository;
        $this->pwdRepository = $pwdRepository;

    }

    public function getByEmail($data){
        $validator = Validator::make($data, [
            'email' => 'required'
        ]);


        if($validator->fails()){
            throw new InvalidArgumentException($validator->errors()->first());
        }

        $result = $this->userRepository->getUserByEmail($data['email']);

        return $result;
    }

    public function create($data){
        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required|min:6',
            'confirmation_password' => 'required|min:6'
        ]);

        if($validator->fails()){
            throw new InvalidArgumentException($validator->errors()->first());
        }

        $result = $this->userRepository->save($data);

        return $result;
    }

    public function updateVerification($data){
        $validator = Validator::make($data, [
            'email' => 'required'
        ]);

        if($validator->fails()){
            throw new InvalidArgumentException($validator->errors()->first());
        }

        $result = $this->userRepository->updateEmailVerified($data['email']);

        return $result;
    }

    public function updatePassword($data){
        $validator = Validator::make($data, [
            'password' => 'required|min:6'
        ]);

        if($validator->fails()){
            throw new InvalidArgumentException($validator->errors()->first());
        }

        $result = $this->userRepository->updatePassword($data);

        return $result;
    }

    public function getPasswordToken($data){
        $validator = Validator::make($data, [
            'email' => 'required'
        ]);

        if($validator->fails()){
            throw new InvalidArgumentException($validator->errors()->first());
        }

        $result = $this->pwdRepository->getPwdToken($data);

        return $result;
    }

    public function checkToken($data){
        $result = $this->pwdRepository->checkToken($data);

        return $result;
    }

    public function deleteToken($data){
        $result = $this->pwdRepository->deleteToken($data);

        return $result;
    }
}
