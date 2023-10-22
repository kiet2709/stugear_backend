<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

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

    public function searchByName($q)
    {
        $products = $this->model->where('name','LIKE','%'. $q .'%')->get();
        return $products;
    }

    public function attachTag($id, $tags)
    {
        $product = $this->model->find($id);
        $tagIds = DB::table('product_tags')
        ->where('product_id', $id)
        ->pluck('tag_id')
        ->toArray();
        foreach ($tags as $key => $tag) {
            if (in_array($tag, $tagIds)) {
                unset($tags[$key]);
            }
        }
        // $tag = array_diff_assoc($tagIds, $tags);
        if (empty($tags)) {
            return DB::table('product_tags')
            ->where('product_id', $id)
            ->pluck('tag_id')
            ->toArray();
        }
        foreach ($tags as $tag) {
            $insertData[] = [
                'product_id' => $id,
                'tag_id' => $tag,
            ];
        }
        DB::table('product_tags')->insert($insertData);
        $result = DB::table('product_tags')
        ->where('product_id', $id)
        ->pluck('tag_id')
        ->toArray();
        return $result;
    }

}
