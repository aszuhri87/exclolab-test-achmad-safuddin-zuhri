<?php

namespace App\Http\Controllers;

use App\Mail\SendResetPassword;
use App\Mail\SendResetPssword;
use App\Mail\SendVerification;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Laravel\Passport\Passport;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;

class AuthController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function register(Request $request){
        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'confirmation_password' => $request->confirmation_password
            ];

            $result = $this->service->create($data);
            $link = url("/api/verification/?email=". $request->email);

            Mail::to($request->email)->send(new SendVerification($link));

            return response()->json(['message' => 'Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function forgot_password(Request $request){
        try {
            $data = ['email' => $request->email];
            $user_check = $this->service->getByEmail($data);


            if(!$user_check){
                return response()->json(['message' => 'email not found'], 404);
            }

            Password::sendResetLink(
                $request->only('email')
            );

            $token = $this->service->getPasswordToken($request->email);

            $link = url("api/verify-token/?token=".$token);

            Mail::to($request->email)->send(new SendResetPassword($link));

            return response()->json(['message' => 'Success'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function verify_token($token){
        try {
            $check = $this->service->checkToken($token);

            if(!$check){
                return response()->json(['message' => 'not verified'], 400);
            }

            return response()->json(['data'=> ['verify' => true], 'message' => 'Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update_password(Request $request, $token){
        try {
            $check = $this->service->checkToken($token);

            if(!$check){
                return response()->json(['message' => 'not verified'], 400);
            }

            $data = ['email' => $check->email];
            $user_check = $this->service->getByEmail($data);

            if(!$user_check){
                return response()->json(['message' => 'email not found'], 404);
            }

            if (!$request->password != $request->confirmation_password){
                return response()->json(['message' => 'Not match'], 400);
            }

            $resetToken = $this->service->deleteToken($token);

            $result = $this->service->updatePassword(Hash::make($request->password));

            return response()->json(['message' => 'Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function verify($email){
        try {
            $data = ['email' => $email];
            $user_check = $this->service->getByEmail($data);

            if(!$user_check){
                return response()->json(['message' => 'User not found'], 404);
            }
            $result = $this->service->updateVerification($data);

            return $result;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);

        }
    }

    public function login(Request $request){
        try {
            $auth_data = [];
            $login = [
                'email' => $request->email,
                'password' => $request->password
            ];

            $user_check = $this->service->getByEmail($login);

            if(!$user_check){
                return response()->json(['message' => 'User not found'], 404);
            }

            $check_password = Hash::check($request->password, $user_check->password);

            if(!$check_password){
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            if(!$user_check->email_verified_at){
                return response()->json(['message' => 'not verified'], 400);
            }

            $attempt = Auth::attempt($login);

            $auth_data['token'] = $user_check->createToken('app');

            if($request->remember_me){
                Passport::tokensExpireIn(Carbon::now()->addMonth(1));
            }

            return response()->json(['data' => $auth_data, 'message' => 'Success'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function logout(){
        try {
            $token = Auth::user()->tokens->pluck('id');
            Token::whereIn('id', $token)->update(['revoked'=>true]);

            RefreshToken::whereIn('acces_token_id', $token)->update(['revoked'=>true]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error'], 500);
        }
    }
}
