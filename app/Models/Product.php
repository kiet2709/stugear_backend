<?php

namespace App\Models;

use Carbon\Carbon;
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
        'condition',
        'edition',
        'origin_price',
        'quantity',
        'status',
        'user_id',
        'category_id',
        'transaction_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
    public function getPriceAttribute()
    {
        // Retrieve the price from the database
        $price = $this->attributes['price'];

        // Format the price as desired (adding commas and appending 'VNĐ')
        $formattedPrice = number_format($price) . 'VNĐ';

        return $formattedPrice;
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }

    public function getStatusAttribute($value)
    {
        return $value == 1 ? 'Còn hàng' : 'Hết hàng';
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productTags()
    {
        return $this->hasMany(ProductTag::class);
    }
}
