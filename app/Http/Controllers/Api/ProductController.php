<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(){
        try{
            $products = Product::with(['category', 'brand' , 'creator', 'images'])
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
                'create_by' => 'required|exists:users,id',
                'images' => 'sometimes|array',
                'images.*' => 'required|string' 

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

            //Handle upload images
             if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $imagePath) {
                    $product->images()->create([
                        'image' => $imagePath
                    ]);
                }
            }

            $product->load(['category', 'brand', 'creator', 'images']);

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
            $product = Product::with(['category', 'brand', 'creator', 'images'])->find($id);
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
            $product = Product::with('images')->find($id);
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
                'create_by' => 'sometimes|required|exists:users,id',
                'images' => 'sometimes|array',
                'images.*' => 'required|string'
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

            // Handle upload images
                        if (isset($data['images']) && is_array($data['images'])) {
                // Delete existing images (optional - remove if you want to keep old images)
                // $product->images()->delete();
                
                // Add new images
                foreach ($data['images'] as $imagePath) {
                    $product->images()->create([
                        'image' => $imagePath
                    ]);
                }
            }



            //load relationships
            $product->load(['category', 'brand', 'creator', 'images']);

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

    //add image
   public function addImage(Request $request, $id){
    try{
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // FIX: Validate single image file upload
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Store the uploaded image file
        $imagePath = $request->file('image')->store('products', 'public');

        // Create product image record
        $productImage = $product->images()->create([
            'image' => $imagePath
        ]);

        // Load the updated images relationship
        $product->load('images');

        return response()->json([
            'success' => true,
            'data' => $product, // Return full product with images
            'message' => 'Image added successfully'
        ], 201);
        
    }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to add image',
            'error' => $e->getMessage()
        ],500);
    }
}
    //delete image
   public function deleteImage($id, $imageId){
    try{
        $image = ProductImage::where('product_id', $id)->where('id', $imageId)->first();

        if(!$image){
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        }

        // Delete the physical image file from storage
        if ($image->image && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        // Delete the database record
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ], 200);

    }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete image',
            'error' => $e->getMessage()
        ],500);
    }
}


}
