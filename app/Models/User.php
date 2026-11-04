<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// these are Laravel 13 newer features that allow you to define the fillable and hidden attributes of the model using attributes instead of properties. This can make the code cleaner and easier to read.
#[Fillable(['name', 'email', 'password'])] // This attribute is used to specify which attributes of the model are mass assignable. In this case, the 'name', 'email', and 'password' attributes can be mass assigned.
#[Hidden(['password', 'remember_token'])] // This attribute is used to specify which attributes of the model should be hidden when the model is converted to an array or JSON. In this case, the 'password' and 'remember_token' attributes will be hidden.
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens; // This line is using three traits in the User model. The HasFactory trait allows the model to use factories for creating instances of the model. The Notifiable trait allows the model to send notifications. The HasApiTokens trait allows the model to issue API tokens for authentication.

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

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
}
