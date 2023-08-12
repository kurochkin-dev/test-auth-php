<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\IndexProductRequest;
use App\Http\Requests\Api\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductOption;

class ProductController extends Controller
{
    public function index(IndexProductRequest $request)
    {

        $query = Product::with(['options' => function ($query) {
            $query->select(['id', 'product_id', 'name', 'value']);
        }]);

        $optionsFilters = $request->input('options', []);
        foreach ($optionsFilters as $options_name => $options_values) {
            $query->orWhereHas('options', function ($query) use ($options_name, $options_values) {
                $query->whereIn('name', [$options_name])
                    ->whereIn('value', $options_values);
            });
        }

        // Фильтрация по количеству
        $quantityFilter = $request->input('quantity');
        if ($quantityFilter !== null) {
            $query->where('quantity', '>=', $quantityFilter);
        }

        $products = $query->paginate(10);

        return response()->json($products);
    }

    public function store(StoreProductRequest $request)
    {

        $product = Product::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
        ]);

        // Attach the options to the product
        foreach ($request->input('options') as $optionData) {
            $option = new ProductOption($optionData);
            $product->options()->save($option);
        }

        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::with(['option' => function ($query) {
            $query->select(['id', 'product_id', 'name', 'value']);
        }])->findOrFail($id);

        return response()->json($product, 200);
    }

}
