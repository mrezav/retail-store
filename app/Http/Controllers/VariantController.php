<?php

namespace App\Http\Controllers;

use App\Http\Resources\VariantResource;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
