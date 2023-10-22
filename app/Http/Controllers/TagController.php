<?php

namespace App\Http\Controllers;

use App\Repositories\Tag\TagRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    protected $tagRepository;


    public function __construct(TagRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 400);
        }
        $colors = [
            'bg-primary',
            'bg-secondary',
            'bg-success',
            'bg-danger',
            'bg-warning',
            'bg-info',
            'bg-light',
            'bg-dark',
            'bg-white'
        ];

        if (!in_array($request->color, $colors)) {
            return response()->json([
                'status' => 'error',
                'message'=> 'Định dạng màu không đúng'
            ]);
        }

        $tag = $this->tagRepository->findByTagName($request->name);

        if ($tag) {
            return response()->json([
                'status' => 'error',
                'message'=> 'Tag có rồi, không được thêm nữa!'
            ]);
        }

        $tag = $this->tagRepository->save([
            'name' => $request->name,
            'color'=> $request->color,
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_at' => Carbon::now(),
            'updated_at'=> Carbon::now()
        ]);

        if (!$tag) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Tạo tag thất bại',
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Tạo tag thành công',
                'data' => $tag
            ]);
        }
    }

    public function view($id)
    {
        $tag = $this->tagRepository->getById($id);
        if (!$tag) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Lấy dữ liệu tag thất bại',
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu tag thành công',
                'data' => $tag
            ]);
        }
    }
}
