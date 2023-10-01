<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
         if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 400);
        }

        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        $user->refresh_token = Uuid::uuid4();

        $this->userRepository->save([
            'refresh_token' => $user->refresh_token,
            'token_expired' => Carbon::now()->addDays(4)
        ], $user->id);

        return response()->json([
                'status' => 'success',
                'message' => 'login sucessfully',
                'data' => [
                    'access_token' => $token,
                    'refresh_token' => $user->refresh_token,
                    'role' => DB::table('user_roles')
                        ->where('user_id', $user->id)
                        ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                        ->pluck('roles.role_name')
                        ->toArray()
                ]
            ]);

    }

    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:password',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255'
        ]);
         if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 400);
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);

        $user = $this->userRepository->save($data);

        DB::table('user_roles')->insert([
            'user_id' => $user->id,
            'role_id' => 2
        ]);

        DB::table('users')->where('id', $user->id)
            ->update([
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => [
                'email' => $user->email
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh(Request $request)
    {
        $token = $request->header();
        $bareToken = substr($token['authorization'][0], 7);
        $parts = explode('.', $bareToken);
        $payload = json_decode(base64_decode($parts[1]));
        $user = $this->userRepository->getById($payload->id);

        return response()->json([
                'status' => 'success',
                'message' => 'refresh token sucessfully',
                'data' => [
                    'access_token' => $token,
                    'refresh_token' => $user->refresh_token,
                    'role' => DB::table('user_roles')
                        ->where('user_id', $user->id)
                        ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                        ->pluck('roles.role_name')
                        ->toArray()
                ]
            ]);
    }

}