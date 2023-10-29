<?php

namespace App\Repositories\Comment;

use App\Models\Comment;
use App\Repositories\BaseRepository;
use App\Repositories\Comment\CommentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public function getModel()
    {
        return Comment::class;
    }

    public function getCommentByProductId($id)
    {
        $result = DB::table('comments')
            ->where('product_id', $id )->get();
        return $result;
    }

    public function getCommentByParentId($id)
    {
        $result = DB::table('comments')
        ->where('parent_id', $id )->get();
        return $result;
    }
}
