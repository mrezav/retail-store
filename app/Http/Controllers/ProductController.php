<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Category;
use Illuminate\Support\Arr;
use illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\VariantResource;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::get(['id', 'name']);
        $products = Product::with('category', 'variants')
            ->when($request->has('filters.keyword'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('filters.keyword') . '%');
            })
            ->when($request->has('filters.category_id'), function ($q) use ($request) {
                $q->when($request->filled('filters.category_id'), fn($q) => $q->where('category_id', $request->input('filters.category_id')));
            })
            ->paginate(8)->withQueryString();

        return Inertia::render('products/Index', [
            'products' => ProductResource::collection($products),
            'categories' => $categories,
            'search' => $request->filters
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::get(['id', 'name']);
        return Inertia::render('products/Create', ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'variants' => 'required',
        ]);

        // if($data->variants){
        //     dd($request->variants);
        // }else{
        //     throw ValidationException::withMessages([
        //         'variants' => 'Silahkan tambah variant terlebih dahulu!'
        //     ]);
        //     // throw new HttpException(400, 'salah input data');
        // }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('products', $fileName, 'public');
        }

        $slug = Str::slug($request->name);
        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'image_path' => $imagePath ?? null,
            'category_id' => $request->category_id,
            'is_active' => true,
        ]);

        if ($request->variants) {
            $product->variants()->createMany($request->variants);
        }

        return redirect()->route('products.index')->with('message', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $product = Product::with('category')->find($id);
        $variants = Variant::where('product_id', $id)
            ->when($request->has('keyword'), function ($query) use ($request) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                // $q->where('merk', 'like', "%{$keyword}%")
                //     ->orWhere('color', 'like', "%{$keyword}%")
                //     ->orWhere('dimension', 'like', "%{$keyword}%");
                $q->where(DB::raw("CONCAT(merk, ' ', color, ' ', dimension)"), 'like', "%{$keyword}%")
                    ->orWhere(DB::raw("CONCAT(merk, ' ', dimension)"), 'like', "%{$keyword}%");
            });
        })
        ->paginate(8);

        return Inertia::render('products/Detail', [
            'product' => new ProductResource($product),
            'variants' => VariantResource::collection($variants),
            'search' => $request->keyword
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categories = Category::get(['id', 'name']);
        // $product = Product::with('variants')->where('id', $id)->get();
        $product = new ProductResource(Product::findorfail($id)->load('variants'));
        return Inertia::render('products/Edit', ['categories' => $categories, 'product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'variants' => 'required',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('products', $fileName, 'public');
        }

        $slug = Str::slug($request->name);
        $product = Product::findorfail($id);
        $product->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'image_path' => $imagePath ?? $product->image_path,
            'category_id' => $request->category_id,
            'is_active' => true,
        ]);

        if ($request->variants) {
            $variants = Variant::where('product_id', $id)->get();

            $new_variant = [];
            $ids = [];
            foreach ($request->variants as $variant_req) {
                if ($variant_req['id'] != null) {
                    $variants->transform(function ($val) use ($variant_req) {
                        if ($variant_req['id'] == $val->id) {
                            $val->merk = $variant_req['merk'];
                            $val->color = $variant_req['color'];
                            $val->unit = $variant_req['unit'];
                            $val->dimension = $variant_req['dimension'];
                            $val->stock = $variant_req['stock'];
                            $val->price = $variant_req['price'];
                        }
                        return $val;
                    });
                    array_push($ids, $variant_req['id']);
                } else {
                    array_push($new_variant, $variant_req);
                }
            }

            $sql = "UPDATE variants SET merk = CASE id ";
            foreach ($variants as $variant) {
                $sql .= " WHEN {$variant['id']} THEN " . DB::getPdo()->quote($variant->merk);
            }
            $sql .= " END, color = CASE id ";
            foreach ($variants as $variant) {
                $sql .= " WHEN {$variant['id']} THEN " . DB::getPdo()->quote($variant->color);
            }
            $sql .= " END, unit = CASE id ";
            foreach ($variants as $variant) {
                $sql .= " WHEN {$variant['id']} THEN " . DB::getPdo()->quote($variant->unit);
            }
            $sql .= " END, dimension = CASE id ";
            foreach ($variants as $variant) {
                $sql .= " WHEN {$variant['id']} THEN " . DB::getPdo()->quote($variant->dimension);
            }
            $sql .= " END, stock = CASE id ";
            foreach ($variants as $variant) {
                $sql .= " WHEN {$variant['id']} THEN " . DB::getPdo()->quote($variant->stock);
            }
            $sql .= " END, price = CASE id ";
            foreach ($variants as $variant) {
                $sql .= " WHEN {$variant['id']} THEN " . DB::getPdo()->quote($variant->price);
            }

            $sql .= " END WHERE id IN (" . implode(',', $ids) . ")";

            DB::statement($sql);

            $product->variants()->createMany($new_variant);
        }

        return redirect()->route('products.index')->with('message', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findorfail($id);
        $product->delete();

        return redirect()->route('products.index')->with('message', 'Berhasil menghapus barang!');
    }
}
