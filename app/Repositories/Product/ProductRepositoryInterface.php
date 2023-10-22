<?php

namespace App\Repositories\Product;

use App\Repositories\RepositoryInterface;

interface ProductRepositoryInterface extends RepositoryInterface
{
    public function getProductById($id);

    public function searchByName($q);

    public function attachTag($id, $tags);
}
