<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\VariantRequest;
use App\Http\Resources\VariantResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Exists;

class VariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $products = Product::select('id', 'name')->get();
        $keyword = $request->input('filters.keyword');
        $variants = Variant::query()
            ->join('products', 'products.id', '=', 'variants.product_id')
            ->when($request->filled('filters.product_id'), fn($q) => $q->where('products.id', $request->input('filters.product_id')))
            ->where(function ($q) use ($keyword) {
                $q->whereRaw("CONCAT_WS(' ', products.name, variants.merk, variants.color, variants.dimension) LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("CONCAT_WS(' ', products.name, variants.merk, variants.dimension) LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("CONCAT_WS(' ', products.name, variants.color) LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("CONCAT_WS(' ', products.name, variants.dimension) LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("CONCAT_WS(' ', products.name, variants.color, variants.dimension) LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("CONCAT_WS(' ', variants.merk, variants.color, variants.dimension) LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("CONCAT_WS(' ', variants.merk, variants.dimension) LIKE ?", ["%{$keyword}%"]);
            })
            ->select('variants.*')   // penting: supaya Eloquent tahu primary key
            ->with('product')        // akan execute query terpisah untuk eager load
            ->when($request->filled('filters.sort_by'), function($q) use ($request){
                $q->orderBy($request->input('filters.sort_by'), $request->input('filters.sort_type'));
            },function($q) {
                $q->latest();
            } )
            ->paginate(10)->withQueryString();
        // $variants = vsprintf(str_replace('?', "'%s'", $variants->toSql()), $variants->getBindings());
        // ->toSql();
        // dd($variants);
        return Inertia::render('variants/Index', [
            'products' => $products,
            'variants' => VariantResource::collection($variants),
            'search' => $request->filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::select('id', 'name')->get();
        return Inertia::render('variants/Create', ['products' => $products]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VariantRequest $request)
    {
        $request->validate([]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('variants', $fileName, 'public');
        }

        $variant = Variant::create([
            'merk' => $request->merk,
            'color' => $request->color,
            'dimension' => $request->dimension,
            'stock' => $request->stock,
            'unit' => $request->unit,
            'price' => $request->price,
            'description' => $request->description,
            'image_path' => $imagePath ?? null,
            'product_id' => $request->product_id,
        ]);
        

        return redirect()->route('variants.index')->with('message', 'berhasil membuat varian ' . $variant->merk);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $products = Product::select('id', 'name')->get();
        $variant =  new VariantResource(Variant::findorfail($id)->load('product'));
        return Inertia::render('variants/Edit', [
            'products' => $products,
            'variant' => $variant,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VariantRequest $request, string $id)
    {
        $request->validate([]);
        // dd($request->all());
        $imagePath = null;
        $variant = Variant::findorfail($id);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('variants', $fileName, 'public');
            if($variant->image_path){
                if(Storage::disk('public')->exists($variant->image_path)){
                    Storage::disk('public')->delete($variant->image_path);
                }
            }
        }

        $variant = $variant->update([
            'product_id' => $request->product_id,
            'merk' => $request->merk,
            'color' => $request->color,
            'dimension' => $request->dimension,
            'stock' => $request->stock,
            'unit' => $request->unit,
            'price' => $request->price,
            'description' => $request->description,
            'image_path' => $imagePath ?? $variant->image_path,
        ]);
        
        return redirect()->route('variants.index')->with('message', 'berhasil mengubah varian '. $request->merk);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $variant = Variant::findorfail($id);
        if (Storage::disk('public')->exists($variant->image_path)) {
            Storage::disk('public')->delete($variant->image_path);
        }
        // $filePath = public_path($imagePath);
        // if (file_exists($filePath)) {
        //     unlink($filePath);
        // }

        $variant->delete();
        return redirect()->route('variants.index')->with('message', 'Berhasil hapus varian ' . $variant->merk);
    }
}
