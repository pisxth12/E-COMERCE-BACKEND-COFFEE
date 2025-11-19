<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(){
     
        try{
            $brands = Brand::with('category')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

            return response()->json([
                'success' => true,
                'data' => $brands,
                'message' => 'Brands retrieved successfully'
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brands',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //store
    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:brands,name',
                'image' => 'nullable',
                'status' => 'sometimes|in:active,inactive',
                'category_id' => 'required|exists:categories,id'
            ]);
            if($validator->fails()){
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            if($request->hasFile('image')){
                $data['image'] = $request->file('image')->store('brands', 'public');
            }
            $brand = Brand::create($data);
            $brand->load('category');
            return response()->json([
                'success' => true,
                'data' => $brand,
                'message' => 'Brand stored successfully'
            ], 201);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to store brand',
                'error' => $e->getMessage()
            ], 500);
        }   
    }

    //show
    public function show($id){
        try{

            $brand = Brand::find($id);
            
            if(!$brand){
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $brand,
                'message' => 'Brand retrieved successfully' 
            ]);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brand',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //update
    public function update(Request $request , $id){
        try{
            $brand = Brand::find($id);
            if(!$brand){
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found'
                ], 404);
            }

            $validator = Validator::make($request->all(),[
                'name' => 'required|string|max:255|unique:brands,name,' . $id,
                'image' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required'
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
                if($brand->image && Storage::exists($brand->image)){
                    Storage::delete($brand->image);
                }
                $data['image'] = $request->file('image')->store('brands', 'public');
                
            }
            $brand->update($data);

            $brand->load('category');
            return response()->json([
                'success' => true,
                'data' => $brand,
                'message' => 'Brand updated successfully'
            ], 200);
            

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to update brand',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //destroy
    public function destroy($id){
        try{
            $brand = Brand::find($id);
            if(!$brand){
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found'
                ], 404);
            }

            // if($brand->products->count() > 0){
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Cannot delete brand. It has associated products.'
            //     ], 422);
            // }

            //delete image
            if($brand->image && Storage::exists($brand->image)){
                Storage::delete($brand->image);
            }



            $brand->delete();
            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully'
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete brand',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
