<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\OptionValue;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOptionValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['categories', 'images', 'variations'])->withSum('variations', 'quantity')->paginate(10); // Lấy sản phẩm kèm hình ảnh

        return ProductResource::collection($products);
    }

    public function show($id)
    {
        // dd($id);
        // $product = Product::with(['categories', 'images','variations'])->findOrFail($id);
        $product = Product::with([
            'categories',
            'images',
            'variations.color',
            'variations.size',
        ])->findOrFail($id);

        return new ProductResource($product);
    }

    public function create(Request $request)
    {
        // return response()->json($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'variations' => 'required|array|min:1',
            'variations.*.color' => 'required|string|max:50',
            'variations.*.size' => 'required|string|max:50',
            'variations.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 402);
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        // Gắn danh mục cho sản phẩm
        $product->categories()->sync($request->category_ids);

        // Lưu ảnh sản phẩm
        if ($request->hasFile('images') && count($request->images) > 0) {
            foreach ($request->images as $image) {
                $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
                $path = $image->storeAs('products', $filename, 'public_uploads');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => 'uploads/'.$path,
                ]);
            }
        }

        // Lưu các biến thể (variations) của sản phẩm
        foreach ($request->variations as $variation) {
            $color = OptionValue::firstOrCreate([
                'type' => '2', // 2: color
                'name' => $variation['color'],
            ]);

            $size = OptionValue::firstOrCreate([
                'type' => '1', // 1: size
                'name' => $variation['size'],
            ]);

            ProductOptionValue::create([
                'product_id' => $product->id,
                'color_id' => $color->id,
                'size_id' => $size->id,
                'quantity' => $variation['quantity'],
            ]);
        }

        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        foreach ($product->images as $image) {

            $urlCheck = str_replace('uploads/', '', $image->image_url);
            if (Storage::disk('public_uploads')->exists($urlCheck)) {
                Storage::disk('public_uploads')->delete($urlCheck);
            }
        }
        // dd($product);
        $product->categories()->detach(); // Xóa quan hệ với các danh mục
        $product->variations()->forceDelete(); // Xóa hẳn các biến thể của sản phẩm

        // Xóa sản phẩm
        $product->forceDelete();

        // $product->delete();
        return response()->noContent();
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        // return $request->all();

        // dd(1);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
            'new_images' => 'sometimes|array|min:1|max:3',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'variations' => 'required|array|min:1',
            'variations.*.color' => 'required|string|max:50',
            'variations.*.size' => 'required|string|max:50',
            'variations.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::findOrFail($id);

        $product->update([
            'name' => $request->name,
            'description' => $request->has('description') ? $request->description : $product->description,
            'price' => $request->price,
        ]);

        $product->categories()->sync($request->category_ids);

        if ($request->has('new_images') && count($request->new_images) > 0) {
            foreach ($product->images as $image) {
                $urlCheck = str_replace('uploads/', '', $image->image_url);
                if ($image->image_url && Storage::disk('public_uploads')->exists($urlCheck)) {
                    Storage::disk('public_uploads')->delete($urlCheck);
                }
            }
            $product->images()->delete();
            foreach ($request->new_images as $image) {
                $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
                $path = $image->storeAs('products', $filename, 'public_uploads');

                $product->images()->create([
                    'image_url' => 'uploads/'.$path,
                ]);
            }
        }

        // $product->variations()->delete();
        // Dùng đoạn trên chỉ xóa mềm trong khi bảng product option value
        // không cho các giá trị trùng lặp nên khi tạo lại sẽ bị lỗi nên phải xóa thật khỏi bảng luôn
        $product->variations()->forceDelete();
        foreach ($request->variations as $variation) {
            $size = OptionValue::firstOrCreate([
                'type' => 1,
                'name' => $variation['size'],
            ]);
            $color = OptionValue::firstOrCreate([
                'type' => 2,
                'name' => $variation['color'],
            ]);
            $product->variations()->create([
                'color_id' => $color->id,
                'size_id' => $size->id,
                'quantity' => $variation['quantity'],
            ]);
        }

        return response()->noContent();
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $products = Product::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->get();

        // dd($products);
        return ProductResource::collection($products);
        // dd($request->input('qu'));
    }

    public function getProductsByCategory($id)
    {
        $category = Category::findOrFail($id);

        $products = $category->products()->paginate(8);

        return response()->json([
            'data' => ProductResource::collection($products)->response()->getData(true),
            'categoryName' => $category->name,
        ]);
    }
}
