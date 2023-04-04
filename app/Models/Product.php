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
        'quantity',
    ];

    /**
     * Get the options for the product.
     */
    public function options()
    {
        return $this->hasMany(ProductOption::class);

    }

    /**
     * Get the properties for the product.
     */
    public function properties()
    {
        return $this->hasMany(ProductProperty::class);
    }

    /**
     * Scope a query to only include products with the specified options.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $options
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithOptions($query, $options)
    {
        foreach ($options as $key => $values) {
            $query->whereHas('options', function ($query) use ($key, $values) {
                $query->whereIn('name', $key)
                    ->whereIn('value', $values);
            });
        }
        return $query;
    }

    /**
     * Create a new product.
     *
     * @param array $attributes
     * @return Product
     */
    public static function createProduct(array $attributes)
    {
        $product = new static($attributes);
        $product->save();
        return $product;
    }
}
