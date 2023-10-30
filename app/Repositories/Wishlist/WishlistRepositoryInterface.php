<?php

namespace App\Repositories\Wishlist;

use App\Repositories\RepositoryInterface;

interface WishlistRepositoryInterface extends RepositoryInterface
{
    public function getWishlistByUserId($userId, $limit);
}
