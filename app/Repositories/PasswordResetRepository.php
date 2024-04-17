<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class PasswordResetRepository{
    public function getPwdToken($data){
        $result = DB::table('password_resets')->where('email', $data)->first();

        return $result;
    }

    public function checkToken($data){
        $result = DB::table('password_resets')->where('token', $data)->first();

        return $result;
    }

    public function deleteToken($data){
        $result = DB::table('password_resets')->where('token', $data)->delete();

        return $result;
    }
}
