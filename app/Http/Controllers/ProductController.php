<?php

namespace App\Http\Controllers;

use App\Repositories\Product\ProductRepositoryInterface;
use App\Util\ImageService;
use Illuminate\Http\Request;
use App\Util\AppConstant;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        $products = $this->productRepository->getAll();
        return response()->json([
            'status' => 'success',
            'message' => 'get data sucesss',
            'data' => $products
        ]);
    }
    public function view($id)
    {
        $product = $this->productRepository->getById($id);
        if (!$product)
        {
            return response()->json([
            'status' => 'error',
                'message' => 'not found product'
            ], 404);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'found this product',
                'data' => $product
            ]);
        }
    }

    public function uploadImage(Request $request, $id){
        $message = ImageService::uploadImage($request, $id, AppConstant::$UPLOAD_DIRECTORY_PRODUCT_IMAGE, 'products');
        return response()->json([
            'message' => $message
        ]);
    }
    public function getImage($id) {
        $path = ImageService::getPathImage($id, 'products');
        if (str_contains($path, 'uploads')){
            header('Content-Type: image/jpeg');
            readfile($path);
        } else {
            return response()->json([
                'message' => $path
            ]);
        }
    }

    public function searchByName(Request $request) {
        $products = $this->productRepository->searchByName($request->q);
        return response()->json([
            'status' => 'success',
            'message' => 'found this product',
            'data' => $products,
            'count' => count($products)
        ]);

    }

}
