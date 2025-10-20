<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductController extends Controller
{
    public function index()
    {

        $Cache = Cache::remember('products', 3600, function () {
            return Product::paginate(100);
        });

        return ProductResource::collection($Cache->load('Category'));
    }

    public function show($id)
    {
        $products = Product::FindOrFail($id);
        return new ProductResource($products->load('Category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'img' => 'required'
        ]);

        $products = Product::create([
            'user_id' => JWTAuth::user()->id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'qty' => $request->qty,
            'price' => $request->price,
            'img' => $request->img
        ]);

        return new ProductResource($products->load('Category'));
    }

    public function update($id, Request $request)
    {

        $request->validate([
            'category_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'img' => 'required'
        ]);

        $products = Product::FindOrFail($id);

        if (JWTAuth::user()->id == $products->user_id) {
            $products->update([
                'user_id' => JWTAuth::user()->id,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'qty' => $request->qty,
                'price' => $request->price,
                'img' => $request->img
            ]);

            return new ProductResource($products->load('Category'));
        } else {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    public function destroy($id)
    {
        $products = Product::FindOrFail($id);
        $UserID = JWTAuth::user()->id;

        if ($UserID == $products->user_id) {

            $products->delete();
            return response()->json([], 204);
        } {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
    }
}
