<?php

namespace App\Http\Controllers;

use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Wishlist\WishlistRepositoryInterface;
use Carbon\Carbon;
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
            if ($wishlist_product->deleted_by != null || $wishlist_product->deleted_at != null) {
                continue;
            }
            $product = $this->productRepository->getById($wishlist_product->product_id);
            $memberData['id'] = $wishlist_product->product_id;
            $memberData['name'] = $product->name;
            $memberData['price'] = $product->price;
            $memberData['status'] = $product->status;
            array_push($data, $memberData);
        }
        return response()->json([
            'status' => 'Thành công',
            'message' => 'Lấy wishlist thành công',
            'data' => $data
        ]);
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
        $wishlishId = $wishlist_products->first()->wishlist_id;
        foreach ($wishlist_products as $wishlist_product) {
            $product = $this->productRepository->getById($wishlist_product->product_id);
            if ($wishlist_product->product_id == $request->product_id && ($product->deleted_at != null || $product->deleted_by != null)) {
                return response()->json([
                    'fail'=> 'Thất bại',
                    'message'=> 'Không thể thêm sản phẩm vào nữa vì đã thêm rồi!'
                ], 500);
            } else {
                $result = $this->wishlistRepository->updateWishlist([
                    'updated_by' => $request->user_id,
                    'updated_at'=> Carbon::now(),
                    'deleted_by' => null,
                    'deleted_at' => null
                ], $product->id, $wishlishId);
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

        $result = $this->wishlistRepository->addToWishlist([
            'wishlist_id'=> $wishlishId,
            'product_id'=> $product->id,
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_at' => Carbon::now(),
            'updated_at'=> Carbon::now()
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
    public function remove(Request $request, $id)
    {
        $product = $this->productRepository->getById($request->product_id);
        if (! $product || $product->deleted_at != null || $product->deleted_by != null) {
            return response()->json([
                'error'=> 'Có lỗi',
                'message' => 'Không tìm thấy sản phẩm'
            ], 404);
        }

        $wishlish = $this->wishlistRepository->getById($id);
        $wishlist_product = $this->wishlistRepository->getWishlistByIdAndProductId($id, $request->product_id);
        if (! $wishlist_product || $wishlist_product->deleted_at != null || $wishlist_product->deleted_by != null){
            return response()->json([
                'error'=> 'Có lỗi',
                'message' => 'Sản phẩm này không có trong wishlish này để xóa!'
            ], 404);
        }

        $result = $this->wishlistRepository->updateWishlist([
            'updated_by' => $wishlish->user_id,
            'updated_at'=> Carbon::now(),
            'deleted_by' => $wishlish->user_id,
            'deleted_at' => Carbon::now()
        ], $request->product_id, $id);

        if ($result) {
            return response()->json([
                'success'=> 'Thành công',
                'message'=> 'Xóa khỏi wishlist thành công'
            ]);
        } else {
            return response()->json([
                'fail'=> 'Thất bại',
                'message'=> 'Xóa khỏi wishlist thất bại'
            ], 500);
        }
    }
}
