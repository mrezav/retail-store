<?php

namespace App\Http\Controllers;

use App\Http\Resources\VariantResource;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GetVariantsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $keyword = $request->query('keyword');
        $variants = Variant::query()
            ->join('products', 'products.id', '=', 'variants.product_id')
            // ->when($request->filled('filters.product_id'), fn($q) => $q->where('products.id', $request->input('filters.product_id')))
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
            // ->when($request->filled('filters.sort_by'), function($q) use ($request){
            //     $q->orderBy($request->input('filters.sort_by'), $request->input('filters.sort_type'));
            // },function($q) {
            //     $q->latest();
            // } )
            ->paginate(10)->withQueryString();
        // $variants = vsprintf(str_replace('?', "'%s'", $variants->toSql()), $variants->getBindings());
        // ->toSql();
        $data = [
            // 'products' => $products,
            'data' => VariantResource::collection($variants),
            'meta' => [
                'current_page' => $variants->currentPage(),
                'from' => $variants->firstItem(),
                'to' => $variants->lastItem(),
                'last_page' => $variants->lastPage(),
                'per_page' => $variants->perPage(),
                'total' => $variants->total(),
                'links' => $variants->linkCollection(),
            ]
        ];
        return response()->json($data);
    }
}
