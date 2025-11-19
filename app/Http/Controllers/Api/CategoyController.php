<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CategoyController extends Controller
{
    public function index(){
        try{
            $categories = Category::where('status', 'active')
            ->orderBy('name')
            ->with('brands')
            ->get();
            

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories retrieved successfully',
            ],200);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //store category
    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'status' => 'sometimes|in:active,inactive'

            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            
            //Handle upload image
            if($request->hasFile('image')){
                $data['image'] = $request->file('image')->store('categories', 'public');
            }


            $category = Category::create($data);
            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category stored successfully',
            ], 201);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //show single category
    public function show($id){
        try{

            $category =  Category::find($id);
            if(!$category){
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            $category->load('brands');
            // $category->load(['brands', 'products']);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category retrieved successfully'
            ], 200);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //update category
    public function update(Request $request , $id){
        try{
            $category = Category::find($id);

            if(!$category){
                return response([
                    'success' => false,
                    'message' => 'Category not found'
                ],404);
            }

            $validator = Validator::make($request->all(), [
                'name'=> 'required|string|max:255|unique:categories,name'. $id,
                'image'=> 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'status'=> 'sometimes|in:active,inactive'        
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            //Handle upload image
            if($request->hasFile('image')){
                //delete old image
                if($category->image && Storage::exists($request->image)){
                    Storage::delete($request->image);
                }

                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            $category->update($data);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category updated successfully'
            ],200);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }   

    //delete category
    public function destroy($id){
        try{
            $category = Category::find($id);

            if(!$category){
                return response([
                    'success' => false,
                    'message' => 'Category not found'
                ],404);
            }

            //chekc is category has products or brands
            // if($category->products->count() > 0){
            //     return response()->json([
            //     'success' => false,
            //     'message' => 'Cannot delete category. It has associated products.'
            //     ],422);
            // }

            // if($category->brands->count() > 0){
            //     return response([
            //         'success' => false,
            //         'message' => 'Cannot delete category. It has associated brands.'
            //     ],422);
            // }
            
            // Delete image if exists
            if($category->image && Storage::exists($category->image)){
                Storage::delete($category->image);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ],200);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Searchcategories by name
    public function search($search){
        try{
            $cateegories = Category::where('name', 'like','%'. $search.'$%')
            ->where('status','active')
            ->orderBy('name')
            ->get();


            return response()->json([
                'success' => true,
                'data' => $cateegories,
                'message' => 'Categories retrieved successfully'
            ],200);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   


}
