<?php

namespace App\Repositories\Wishlist;

use App\Models\Wishlist;
use App\Repositories\BaseRepository;
use App\Repositories\Wishlist\WishlistRepositoryInterface;
use Illuminate\Support\Facades\DB;

class WishlistRepository extends BaseRepository implements WishlistRepositoryInterface
{
    public function getModel()
    {
        return Wishlist::class;
    }

    public function getWishlistByUserId($userId, $limit)
    {
        $wishlistId = $this->model->where("user_id", $userId)->first()->id;
        $results = DB::table("wishlist_products")
            ->where('wishlist_id', $wishlistId)
            ->paginate($limit);
        return $results;
    }
}
