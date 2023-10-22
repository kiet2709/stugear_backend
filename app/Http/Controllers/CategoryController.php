<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Util\AppConstant;
use App\Util\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $category = $this->categoryRepository->getById($id);
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

    public function uploadImage(Request $request, $id){
        $message = ImageService::uploadImage($request, $id, AppConstant::$UPLOAD_DIRECTORY_CATEGORY_IMAGE, 'categories');
        return response()->json([
            'message' => $message
        ]);
    }
    public function getImage($id){
        $path = ImageService::getPathImage($id, 'categories');
        if (str_contains($path, 'uploads')){
            header('Content-Type: image/jpeg');
            readfile($path);
        } else {
            return response()->json([
                'message' => $path
            ]);
        }
    }

    public function getStatisticByCategory($id) {
        $category = $this->categoryRepository->getById($id);
        $products = $category->products;
        $sold = $products->where('condition', 0)->count();
        $tagTotal = 0;
        foreach ($products as $product) {
            $tagTotal = $tagTotal + DB::table('product_tags')->where('product_id', $product->id)->count();
        }
        return response()->json([
            'status' => 'success',
            'message'=> 'Lấy dữ liệu thành công',
            'data' => [
                'id' => $id,
                'total' => count($products),
                'sold' => $sold,
                'tag_total' => $tagTotal
            ]
        ]);
    }
}
