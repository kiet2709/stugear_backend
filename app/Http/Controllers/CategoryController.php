<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        $categories = $this->categoryRepository->getAll();
        return response()->json([
            'status' => 'success',
            'message' => 'get data sucesss',
            'data' => $categories
        ]);
    }

    public function view($id)
    {
        $category = $this->categoryRepository->getCategoryById($id);
        if (!$category)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'not found category'
            ], 404);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'found this category',
                'data' => $category
            ]);
        }
    }
}
