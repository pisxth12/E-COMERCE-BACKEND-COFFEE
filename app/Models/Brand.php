<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'image',
        'status',
        'category_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];


    //Relationships

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function brands(){
        return $this->hasMany(Brand::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }


}
