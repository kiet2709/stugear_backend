<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function getModel()
    {
        return Product::class;
    }

    public function getProductById($id)
    {
        $category = $this->model->find($id);
        if (!$category || $this->model->isDirty('deleted_by') || $this->model->isDirty('deleted_at'))
        {
            return false;
        }

        return $category;

    }

}