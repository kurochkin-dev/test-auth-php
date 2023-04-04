<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Валидация параметров запроса
        $validator = Validator::make($request->all(), [
            'properties.*' => 'array',
            'quantity' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $query = Product::with(['properties' => function ($query) {
            $query->select(['id', 'product_id', 'property_name', 'property_value']);
        }]);

        $propertyFilters = $request->input('properties', []);
        foreach ($propertyFilters as $property_name => $property_values) {
            $query->orWhereHas('properties', function ($query) use ($property_name, $property_values) {
                $query->whereIn('property_name', [$property_name])
                    ->whereIn('property_value', $property_values);
            });
        }

        // Фильтрация по количеству
        $quantityFilter = $request->input('quantity');
        if ($quantityFilter !== null) {
            $query->where('quantity', '>=', $quantityFilter);
        }

        $products = $query->paginate(40);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'options' => 'required|array',
            'options.*.name' => 'required|string|max:255',
            'options.*.value' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400);
        }

        // Create the product
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

        return response()->json($product);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product, 200);
    }

}
