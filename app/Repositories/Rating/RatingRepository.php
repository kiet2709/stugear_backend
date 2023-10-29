<?php

namespace App\Repositories\Rating;

use App\Models\Rating;
use App\Repositories\BaseRepository;
use App\Repositories\Rating\RatingRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RatingRepository extends BaseRepository implements RatingRepositoryInterface
{
    public function getModel()
    {
        return Rating::class;
    }

    public function getRatingByProductId($id)
    {
        $result = DB::table('rating_products')
            ->where('product_id', $id )->get();
        return $result;
    }
}
