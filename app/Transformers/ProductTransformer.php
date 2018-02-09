<?php
/**
 * Created by PhpStorm.
 * User: seth
 * Date: 18-2-9
 * Time: 下午6:54
 */

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    public function transform(Product $product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'transform' => $product->introduction,
            'price' => $product->price / 100,
            'required_params' => $product->required_params,
            'category_id' => $product->category_id
        ];
    }

}