<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\User\UserRepositoryInterface;

class UserController extends Controller
{
    protected $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function index()
    {
        $users = $this->userRepository->getAllUserWithContactDetail();
        return response()->json([
            'status' => 'success',
            'message' => 'get data sucesss',
            'data' => $users
        ]);
    }

    public function view($id)
    {
        $users = $this->userRepository->getUserWithContactDetailById($id);
        return response()->json([
            'status' => 'success',
            'message' => 'get data sucesss',
            'data' => $users
        ]);
    }
}
