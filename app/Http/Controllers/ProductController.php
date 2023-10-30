<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Comment\CommentRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Tag\TagRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Util\ImageService;
use Illuminate\Http\Request;
use App\Util\AppConstant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $productRepository;
    protected $categoryRepository;
    protected $tagRepository;
    protected $userRepository;
    protected $commentRepository;

    public function __construct(ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        TagRepositoryInterface $tagRepository,
        UserRepositoryInterface $userRepository,
        CommentRepositoryInterface $commentRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->userRepository = $userRepository;
        $this->commentRepository = $commentRepository;
    }

    public function index(Request $request)
    {
        $limit = 10;
        $products = $this->productRepository->getAll($limit);
        $data = [];
        $memberData = [];
        foreach ($products as $product) {
            $memberData['id'] = $product->id;
            $memberData['title'] = $product->name;
            $memberData['product_image'] = AppConstant::$DOMAIN . 'api/products/' . $product->id . '/images';
            $memberData['price'] = $product->price;
            $memberData['comment_count'] = count($this->commentRepository->getCommentByProductId($product->id));
            $productTags = $product->productTags;
            $tags = [];
            foreach ($productTags as $productTag) {
                $tagMember['name'] = $productTag->tag->name;
                $tagMember['color'] = $productTag->tag->color;
                array_push($tags, $tagMember);
            }
            $memberData['tags'] = $tags;
            $memberData['description'] = $product->description;
            $memberData['status'] = $product->status;
            $memberData['last_updated'] = $product->updated_at ?? '';
            $memberData['owner_image'] = AppConstant::$DOMAIN . 'api/users/' . $product->user->id . '/images';;
            array_push($data, $memberData);
        }

        return response()->json([
            'status'=> 'success',
            'message'=> 'Lấy dữ liệu thành công',
            'data'=> $data,
            'page' => $request->page,
            'total_page' => $products->lastPage(),
            'total_items' => $products->total()
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
            $data = [];
            $memberData = [];
            $memberData['id'] = $product->id;
            $memberData['title'] = $product->name;
            $memberData['product_image'] = AppConstant::$DOMAIN . 'api/products/' . $product->id . '/images';
            $memberData['price'] = $product->price;
            $memberData['comment_count'] = count($this->commentRepository->getCommentByProductId($product->id));
            $productTags = $product->productTags;
            $tags = [];
            foreach ($productTags as $productTag) {
                $tagMember['name'] = $productTag->tag->name;
                $tagMember['color'] = $productTag->tag->color;
                array_push($tags, $tagMember);
            }
            $memberData['tags'] = $tags;
            $memberData['description'] = $product->description;
            $memberData['status'] = $product->status;
            $memberData['last_updated'] = $product->updated_at ?? '';
            $memberData['owner_image'] = AppConstant::$DOMAIN . 'api/users/' . $product->user->id . '/images';;
            $memberData['owner_name'] = $product->user->name;
            $memberData['owner_id'] = $product->user->id;
            $memberData['quantity'] = $product->quantity;
            $memberData['condition'] = $product->status == 0 ? 'Mới' : 'Đã sử dụng';
            $memberData['transaction_method'] = $product->transaction_id == 0 ? 'Trực tiếp' : 'Trên trang web';
            array_push($data, $memberData);
            return response()->json([
                'status'=> 'success',
                'message'=> 'Lấy dữ liệu thành công',
                'data'=> $data
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

    public function getProductByCategoryId(Request $request, $id) {
        $limit = 10;
        $products = $this->productRepository->getProductByCategoryId($id, $limit);
        $data = [];
        $memberData = [];
        foreach ($products as $product) {
            $memberData['id'] = $product->id;
            $memberData['title'] = $product->name;
            $memberData['product_image'] = AppConstant::$DOMAIN . 'api/products/' . $product->id . '/images';
            $memberData['price'] = $product->price;
            $memberData['comment_count'] = count($this->commentRepository->getCommentByProductId($product->id));
            $productTags = $this->productRepository->getProductTagsByProductId( $product->id );
            $tags = [];
            foreach ($productTags as $productTag) {
                $tag = $this->tagRepository->getById($productTag->tag_id);
                $tagMember['name'] = $tag->name;
                $tagMember['color'] = $tag->color;
                array_push($tags, $tagMember);
            }
            $memberData['tags'] = $tags;
            $memberData['description'] = $product->description;
            $memberData['status'] = $product->status;
            $memberData['last_updated'] = $product->updated_at ?? '';
            $memberData['owner_image'] = AppConstant::$DOMAIN . 'api/users/' . $product->user_id . '/images';;
            array_push($data, $memberData);
        }
        return response()->json([
            'status'=> 'success',
            'message'=> 'Lấy dữ liệu thành công',
            'data'=> $data,
            'page' => $request->page,
            'total_page' => $products->lastPage(),
            'total_items' => $products->total()
        ]);
    }

    public function getProductByTagId(Request $request, $id)
    {
        $limit = 1;
        $productTags = $this->tagRepository->getProductTagsByTagId( $id, $limit );
        dd($productTags);
        $products = [];
        foreach ($productTags as $productTag) {
            $product = $this->productRepository->getById( $productTag->product_id );
            array_push($products, $product);
        }
        $data = [];
        $memberData = [];
        foreach ($products as $product) {
            $memberData['id'] = $product->id;
            $memberData['title'] = $product->name;
            $memberData['product_image'] = AppConstant::$DOMAIN . 'api/products/' . $product->id . '/images';
            $memberData['price'] = $product->price;
            $memberData['comment_count'] = count($this->commentRepository->getCommentByProductId($product->id));
            $productTags = $product->productTags;
            $tags = [];
            foreach ($productTags as $productTag) {
                $tagMember['name'] = $productTag->tag->name;
                $tagMember['color'] = $productTag->tag->color;
                array_push($tags, $tagMember);
            }
            $memberData['tags'] = $tags;
            $memberData['description'] = $product->description;
            $memberData['status'] = $product->status;
            $memberData['last_updated'] = $product->updated_at ?? '';
            $memberData['owner_image'] = AppConstant::$DOMAIN . 'api/users/' . $product->user->id . '/images';;
            $memberData['owner_name'] = $product->user->name;
            $memberData['owner_id'] = $product->user->id;
            $memberData['quantity'] = $product->quantity;
            $memberData['condition'] = $product->status == 0 ? 'Mới' : 'Đã sử dụng';
            $memberData['transaction_method'] = $product->transaction_id == 0 ? 'Trực tiếp' : 'Trên trang web';
            array_push($data, $memberData);
        }
        return response()->json([
            'status'=> 'success',
            'message'=> 'Lấy dữ liệu thành công',
            'data'=> $data,
            'page' => $request->page,
            'total_page' => $productTags->lastPage(),
            'total_items' => $productTags->total()
        ]);
    }

    public function getByCriteria(Request $request)
    {
        if ($request->transaction_method == 'cash')
        {
            $transaction_method = [1];
        } else if ($request->transaction_method == 'online') {
            $transaction_method = [2];
        } else {
            $transaction_method = [1,2];
        }
        $sort = [];
        if ($request->field == 'lastUpdate' && $request->sort == 'increase')
        {
            $filter = ['field' => 'updated_at', 'sort' => 'ASC'];
        }
        if ($request->field == 'lastUpdate' && $request->sort == 'decrease')
        {
            $filter = ['field' => 'updated_at', 'sort' => 'DESC'];
        }
        if ($request->field == 'price' && $request->sort == 'increase')
        {
            $filter = ['field' => 'price', 'sort' => 'ASC'];
        }
        if ($request->field == 'price' && $request->sort == 'decrease')
        {
            $filter = ['field' => 'price', 'sort' => 'DESC'];
        }
        $products = Product::whereIn('transaction_id', $transaction_method)->orderBy($filter['field'],$filter['sort'])->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Lấy dữ liệu thành công',
            'data' => $products
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|integer|min:1',
            'condition' => 'required|in:0,1',
            'edition' => 'required',
            'origin_price' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'user_id' => 'required|integer|min:1',
            'category_id' => 'required|integer|min:1',
            'transaction_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 400);
        }

        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'condition' => strval($request->condition),
            'edition' => $request->edition,
            'origin_price' => $request->origin_price,
            'quantity' => $request->quantity,
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'transaction_id' => $request->transaction_id,
            'description' => $request->description ?? '',
            'created_at' => Carbon::now(),
            'created_by' => $request->user_id,
            'updated_at' => Carbon::now(),
            'updated_by' => $request->user_id,
        ];
        $product = $this->productRepository->save($data);
        if (!$product) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Tạo sản phẩm thất bại',
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Tạo sản phẩm thành công',
                'data' => $product
            ]);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 400);
        }
        $role = DB::table('user_roles')
        ->where('user_id', $request->user_id)
        ->join('roles', 'user_roles.role_id', '=', 'roles.id')
        ->pluck('roles.role_name')
        ->toArray();

        if (in_array('USER', $role) && $request->status == 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không cho phép người dùng tự duyệt sản phẩm'
            ]);
        }
        $this->productRepository->save(['status' => strval($request->status)], $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật trạng thái sản phẩm thành công',
            'data' => $this->productRepository->getById($id)
        ]);
    }

    public function attachTag(Request $request, $id)
    {
        $result = $this->productRepository->attachTag($id, $request->tags);
        return response()->json([
            'status'=> 'success',
            'message'=> 'Gắn tag thành công',
            'data'=> [
                'product_id' => $id,
                'tags' => $result
            ]
        ]);
    }

}
