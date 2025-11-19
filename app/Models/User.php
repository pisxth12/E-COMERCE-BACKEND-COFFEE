<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'department',
        'role',
        'status',
        'password',
        'remember_token',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Accessor for full name
    public function getNameAttribute(){
        return $this->first_name . ' ' . $this->last_name;
    } 

     // Scope for active users
    public function scopeActive($query)  {
        return $query->where('status' , 'active');
        
    }


// scope for search user
    public function scopeSearch($query , string $search){
        return $query->where(function ($q) use ($search){
            $q->where('first_name' , 'like' , "%$search%")
              ->orWhere('last_name', 'like', "%$search%")
              ->orWhere('email' , 'like' , "%$search%")
              ->orWhere('department' , 'like' , "%$search%");

        });
    }

      // New factory method for Laravel 11
    protected static function newFactory(){
        return UserFactory::new();
    }

    public function products(){
        return $this->hasMany(Product::class,'create_by');
    }

}


