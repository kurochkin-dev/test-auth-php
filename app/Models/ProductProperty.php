<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductProperty extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'property_name', 'property_value'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
