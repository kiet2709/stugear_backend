<?php

namespace App\Http\Controllers;

use App\Util\AuthService;
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

    public function getCurrentUserInfo(Request $request)
    {
        $token = $request->header();
        $bareToken = substr($token['authorization'][0], 7);
        $userId = AuthService::getUserId($bareToken);
        $user = $this->userRepository->getById($userId);
        $userInfo = $this->userRepository->getContactDetail($userId);
        $data = [];
        $data['id'] = $user->id;
        $data['email'] = $user->email;
        $data['name'] = $user->name;
        $data['is_verify'] = $user->is_verify_email == 0 ? 'false' : 'true';
        $data['reputation'] = $user->reputation;
        if ($user->image_id == null) {
            if (!isset($userInfo->gender) || $userInfo->gender == 0 || $userInfo->gender == null) {
                $data['image'] = AppConstant::$AVATAR_MALE;
            } else {
                $data['image'] = AppConstant::$AVATAR_FEMALE;
            }
        } else {
            $data['image'] = AppConstant::$DOMAIN . 'api/users/' . $user->id . '/images' ;
        }
        $data['first_name'] = $user->first_name;
        $data['last_name'] = $user->last_name;
        $data['gender'] = $userInfo->gender ?? '';
        $data['city'] = $userInfo->city ?? '';
        $data['province'] = $userInfo->province ?? '';
        $data['district'] = $userInfo->district ?? '';
        $data['ward'] = $userInfo->ward ?? '';
        $data['full_address'] = $userInfo->full_address ?? '';
        $data['phone_number'] = $userInfo->phone_number ?? '';
        $data['birthdate'] = $userInfo->birthdate ?? '';
        $data['social_link'] = $userInfo->social_link ?? '';
        return response()->json([
            'status' => 'Thành công',
            'message' => 'Lấy dữ liệu user thành công',
            'data' => $data
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
