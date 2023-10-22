<?php

namespace App\Repositories\Tag;

use App\Models\Tag;
use App\Repositories\BaseRepository;

class TagRepository extends BaseRepository implements TagRepositoryInterface
{
    public function getModel()
    {
        return Tag::class;
    }

    public function findByTagName($name)
    {
        $tag = $this->model->where("name", $name)->first();
        if ($tag) {
            return $tag;
        } else {
            return false;
        }
    }
}
