<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CategoryController extends Controller
{
    public function index()
    {
        $categorys = Category::all();
        return response()->json([
            'data' => $categorys
        ], 200);
    }

    public function show($id)
    {
        $categorys = Category::findOrFail($id);

        return response()->json([
            'data' => $categorys
        ], 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $categorys = Category::create([
            'title' => $request->title
        ]);

        return response()->json([
            'Message' => 'Success',
            'data' => $categorys
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $categorys = Category::FindOrFail($id);
        $request->validate([
            'title' => 'required'
        ]);

        $categorys->update([
            'title' => $request->title
        ]);

        return response()->json([
            "Message" => "Success",
            "Data" => $categorys
        ], 201);
    }

    public function destroy($id)
    {
        $categorys = Category::FindOrFail($id);
        $categorys->delete();

        return response()->json([], 404);
    }
}
