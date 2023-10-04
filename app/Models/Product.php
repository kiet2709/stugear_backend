<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'description',
        'excellent',
        'good',
        'bad',
        'old',
        'edition',
        'origin_price',
        'quantity',
        'available',
        'unavailable',
        'user_id',
        'category_id',
        'transaction_id'
    ];
}
