<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
// use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Str;
use Symfony\Component\HttpFoundation\Response;
class UserController extends Controller
{
    public function index(Request $request){
        $search = $request->query('search' , '');

        try {
            $query = User::query();

            //search functionnality
            if($request->has('search') && !empty($search)){
                $query->search($search);
            }
            

            //filter by status
            if($request->has('status') && $request->status !=''){
                $query->where('status', $request->status);
            }

            //filter by role
            if($request->has('role') && $request->role !=''){
                $query->where('role', $request->role);
            }

            //filter by department
            if($request->has('department') && $request->department !=''){
                $query->where('department', $request->department);
            }

            //shorting
            $sortField = $request->get('sort_field' , 'created_at');
            $sortDierection = $request->get('sort_direction' , 'desc');
            $query->orderBy($sortField, $sortDierection);
            
            // $users = User::get();
            $users = $query->with('products')->get();

            return response()->json([
                'status' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //store user
   public function store(StoreUserRequest $request)
{
    try {
        $userData = $request->validated();
        
        // Force set password for testing
        $userData['password'] = bcrypt(Str::password(12));

        $user = User::create($userData);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'User stored successfully',
        ], Response::HTTP_CREATED);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to store user',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
    //show user 
    public function show(User $user){
        try {
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User retrieved successfully',
            ], Response::HTTP_OK);
        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    //update user
    public function update(UpdateUserRequest $request , User $user){
        try {
            $user->update($request->validated());
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User updated successfully',
            ], Response::HTTP_OK);


        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //destroy user

    public function destroy(User $user){
    try {
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ], Response::HTTP_OK);
    }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete user',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    }

    //search user
    public function search(String $search){
        try{
            $users = User::search($search)->get();
            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully',
            ], Response::HTTP_OK);
        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //update status
    public function updateStatus(Request $request , User $user){
        try{
            $request->validate([
                'status' => 'required|in:active,inactive,suspended,pending',
            ]);
            $user->update([
                'status'=>$request->status
            ]);
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User status updated successfully',
            ], Response::HTTP_OK);
        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }






}