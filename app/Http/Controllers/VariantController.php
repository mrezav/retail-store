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
        // $categories = Category::select('id','name')->get();
        $products = Product::select('id', 'name')
                    ->when($request->filled('filters.category_id'), function($query) use ($request) {
                        $query->where('category_id', $request->filters['category_id']);
                    })
                    ->get();
        $variants = Variant::with('product')
                ->when($request->has('filters.product_id'), function($q) use ($request) { 
                    $q->when($request->filled('filters.product_id'), fn($q) => $q->where('product_id', $request->input('filters.product_id')));
                })
                ->when($request->has('filters.keyword'), function($query) use ($request) {
                    $query->when($request->filled('filters.keyword'), function($q) use ($request){
                        $keyword = $request->input('filters.keyword');
                        $q->where(DB::raw("CONCAT(merk, ' ', color, ' ', dimension)"), 'like', "%{$keyword}%")
                            ->orWhere(DB::raw("CONCAT(merk, ' ', dimension)"), 'like', "%{$keyword}%");
                    });
                })
                // ->toSql();
                ->paginate(10)->withQueryString();
        // dd($variants);
        return Inertia::render('variants/Index',[
            // 'categories' => $categories,
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
