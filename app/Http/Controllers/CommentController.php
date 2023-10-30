<?php

namespace App\Http\Controllers;

use App\Repositories\Comment\CommentRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use App\Util\AppConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    protected $commentRepository;
    protected $userRepository;

    public function __construct(CommentRepositoryInterface $commentRepository, UserRepositoryInterface $userRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
    }

    public function getCommentByProductId($productId)
    {
        Carbon::setLocale('vi');
        $comments = $this->commentRepository->getCommentByProductId($productId);
        $data = [];
        $memberData = [];
        foreach ($comments as $comment) {
            $ownerComment = $this->userRepository->getById($comment->owner_id);
            $memberData['id'] = $comment->id;
            $memberData['owner_name'] = $ownerComment->name;
            $memberData['owner_image'] = AppConstant::$DOMAIN . 'api/users/' . $comment->owner_id . '/images';
            $memberData['content'] = $comment->content;
            $memberData['vote'] = $comment->vote;
            $memberData['last_updated'] = Carbon::parse($comment->updated_at)->diffForHumans(Carbon::now());
            $subCommentData = [];
            $subCommentMember = [];
            $subComments = $this->commentRepository->getCommentByParentId($comment->id);
            foreach ($subComments as $subComment) {
                $ownerSubComment = $this->userRepository->getById($subComment->owner_id);
                $subCommentMember['id'] = $subComment->id;
                $subCommentMember['owner_name'] = $ownerSubComment->name;
                $subCommentMember['owner_image'] = AppConstant::$DOMAIN . 'api/users/' . $subComment->owner_id . '/images';
                $subCommentMember['content'] = $subComment->content;
                $subCommentMember['vote'] = $subComment->vote;
                $user = $this->userRepository->getById($subComment->reply_on);
                $subCommentMember['reply_on'] = $user->name;
                $subCommentMember['last_updated'] = Carbon::parse($subComment->updated_at)->diffForHumans(Carbon::now());
                array_push($subCommentData, $subCommentMember);
            }
            $memberData['sub_comment'] = $subCommentData;
            array_push($data, $memberData);
        }
        return response()->json([
            'status' => 'Thành công',
            'message' => 'Lấy dữ liệu thành công',
            'data' => $data
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|min:1',
            'product_id' => 'required|integer|min:1',
            'parent_id' => 'required|integer|min:0',
            'reply_on' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 400);
        }

        $result = $this->commentRepository->save([
            'content' => $request->input('content'),
            'owner_id' => $request->input('user_id'),
            'parent_id' => $request->input('parent_id'),
            'product_id' => $request->input('product_id'),
            'reply_on' => $request->input('reply_on'),
            'vote' => 0,
            'created_by' => $request->input('user_id'),
            'updated_by' => $request->input('user_id'),
            'created_at' => Carbon::now(),
            'updated_at'=> Carbon::now()
        ]);

        if ($result) {
            return response()->json([
                'status'=> 'Thành công',
                'message' => 'Comment thành công',
            ]);
        } else {
            return response()->json([
                'status'=> 'Thất bại',
                'message' => 'Comment thất bại',
            ],500);
        }

    }
}
