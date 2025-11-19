<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'description',
        'qty',
        'status',
        'category_id',
        'brand_id',
        'create_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function creator(){
        return $this->belongsTo(User::class);
    }

    
}
