<?php

namespace App\Http\Controllers;

use App\Util\ImageService;
use App\Util\AppConstant;
use Illuminate\Http\Request;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

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

    public function uploadImage(Request $request, $id){
        $message = ImageService::uploadImage($request, $id, AppConstant::$UPLOAD_DIRECTORY_USER_IMAGE, 'users');
        return response()->json([
            'message' => $message
        ]);
    }
    public function getImage($id){
        $path = ImageService::getPathImage($id, 'users');
        if (str_contains($path, 'uploads')){
            header('Content-Type: image/jpeg');
            readfile($path);
        } else {
            return response()->json([
                'message' => $path
            ]);
        }
    }

    public function updateStatus(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|min:1',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 400);
        }

        $this->userRepository->save([
            'is_enable' => strval($request->status),
            'updated_at'=> Carbon::now(),
            'updated_by' => $request->user_id
        ], $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật trạng thái người dùng thành công',
        ]);
    }
}
