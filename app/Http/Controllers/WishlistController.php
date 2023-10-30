<?php

namespace App\Http\Controllers;

use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Wishlist\WishlistRepositoryInterface;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    protected $wishlistRepository;
    protected $productRepository;
    protected $userRepository;

    public function __construct(WishlistRepositoryInterface $wishlistRepository,
        ProductRepositoryInterface $productRepository,
        UserRepositoryInterface $userRepository)
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }

    public function getWishlistByUserId($id)
    {
        $limit = 10;
        $wishlist_products = $this->wishlistRepository->getWishlistByUserId($id, $limit);
        $data = [];
        $memberData = [];
        foreach ($wishlist_products as $wishlist_product) {
            $product = $this->productRepository->getById($wishlist_product->product_id);
            $memberData['id'] = $wishlist_product->product_id;
            $memberData['name'] = $product->name;
            $memberData['price'] = $product->price;
            $memberData['status'] = $product->status;
            array_push($data, $memberData);
        }
        return response()->json($data);
    }

    public function addProductToWishlist(Request $request)
    {
        $user = $this->userRepository->getById($request->user_id);
        if (! $user) {
            return response()->json([
                'error'=> 'Có lỗi',
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        $product = $this->productRepository->getById($request->product_id);
        if (! $product || $product->deleted_at != null || $product->deleted_by != null) {
            return response()->json([
                'error'=> 'Có lỗi',
                'message' => 'Không tìm thấy sản phẩm'
            ], 404);
        }

        $wishlist_products = $this->wishlistRepository->getWishlistByUserId($request->user_id, 100000);
        foreach ($wishlist_products as $wishlist_product) {
            if ($wishlist_product->product_id == $request->product_id) {
                return response()->json([
                    'fail'=> 'Thất bại',
                    'message'=> 'Không thể thêm sản phẩm vào nữa vì đã thêm rồi!'
                ], 500);
            }
        }

        $result = $this->wishlistRepository->save([
            'user_id'=> $user->id,
            'product_id'=> $product->id,
        ]);
        if ($result) {
            return response()->json([
                'success'=> 'Thành công',
                'message'=> 'Thêm sản phẩm vào wishlist thành công'
            ]);
        } else {
            return response()->json([
                'fail'=> 'Thất bại',
                'message'=> 'Thêm sản phẩm vào wishlist thất bại'
            ], 500);
        }
    }
}
