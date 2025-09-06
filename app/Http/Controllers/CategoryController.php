<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = CategoryResource::collection(Category::latest()->paginate(6));
        return Inertia::render('categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('categories/Create', []);
    }

    public function store(Request $request)
    {
        // Validate and store the category
        $request->validate([
            'name' => 'required|string|unique:categories|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Optional image upload
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $imagePath = $file->storeAs('categories', $fileName, 'public');
            // You can save $imagePath to the database if needed
        }

        $slug = Str::slug($request->name);
        

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'image_path' => $imagePath ?? null,
        ]);

        return redirect()->route('categories.index')->with('message', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return Inertia::render('categories/Edit', [
            'category' => new CategoryResource($category),
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        // Validate and update the category
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Optional image upload
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $imagePath = $file->storeAs('categories', $fileName, 'public');
            // You can save $imagePath to the database if needed
            $category->image_path = $imagePath;
        }

        $slug = Str::slug($request->name);

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            // 'image_path' will be updated only if a new image is uploaded
        ]);

        return redirect()->route('categories.index')->with('message', 'Category updated successfully.');
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('message', 'Category deleted successfully.');
    }
}
