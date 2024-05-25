<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Category::withTrashed()->paginate(5);

        return view('category.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category' => 'required|string|max:30'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            Category::create([
                'name' => $request->category
            ]);
            
            return redirect()->route('category.index')->with('success', 'Category created successfully.');
        } catch (\Throwable $th) {
            info($th);
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $category = Category::withTrashed()->find($id);

            if ($category) {
                return view('category.edit', compact('category'));
            }

            return redirect()->back()->with('error', 'Category not found.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category' => 'required|string|max:30'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            $category = Category::withTrashed()->find($id);

            if ($category) {
                $category->update([
                    'name' => $request->category
                ]);

                return redirect()->route('category.index')->with('success', 'Category updated successfully.');
            }

            return redirect()->back()->with('error', 'Category not found.');
        } catch (\Throwable $th) {
            info($th);
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $category = Category::find($id);

            if ($category) {
                $category->delete();

                return redirect()->back()->with('success', 'Category deleted successfully.');
            }

            return redirect()->back()->with('error', 'Category not found.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function restore($id)
    {
        try {
            $category = Category::withTrashed()->find($id);

            if ($category) {
                $category->restore();

                return redirect()->back()->with('success', 'Category restored successfully.');
            }

            return redirect()->back()->with('error', 'Category not found.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }
}
