<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(){
        try{
            $products = Product::with(['category', 'brand' , 'creator'])
        ->where('status', 'active')
        ->orderBy('id', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Products retrieved successfully'
        ], 200);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage()
            ],500);
        }
    }


    //store product
    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'qty' => 'required|integer|min:0',
                'status' => 'sometimes|in:active,inactive',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
                'create_by' => 'required|exists:users,id'
            ]);
            if($validator->fails()){
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ],422);
            }

            $data = $validator->validated();
            $product = Product::create($data);
            $product->load(['category', 'brand', 'creator']);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product stored successfully'
            ],201);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store product',
                'error' => $e->getMessage()
            ],500);
        }
    }

    //show product
    public function show($id){
        try{
            $product = Product::find($id);
            if(!$product){
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ],404);
            }

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product retrieved successfully'
            ], 200);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product',
                'error' => $e->getMessage()
            ],500);
        }
    }

    //update product
    public function update(Request $request , $id){
        try{
            $product = Product::find($id);
            if(!$product){
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

              $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'price' => 'sometimes|required|numeric|min:0',
                'description' => 'nullable|string',
                'qty' => 'sometimes|required|integer|min:0',
                'status' => 'sometimes|in:active,inactive',
                'category_id' => 'sometimes|required|exists:categories,id',
                'brand_id' => 'sometimes|required|exists:brands,id',
                'create_by' => 'sometimes|required|exists:users,id'
            ]);

              if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $product->update($data);

            //load relationships
            $product->load(['category', 'brand', 'creator']);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product updated successfully'
            ], 200);


        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ],500);
        }
    }

    //destroy product
    public function destroy($id){
        try{
            $product = Product::find($id);
            if(!$product){
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ], 200);


        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ], 500);
        }
    }

}
