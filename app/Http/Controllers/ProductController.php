<?php

namespace App\Http\Controllers;


use App\Models\Order;
use App\Models\Product;
use App\Transformers\ProductTransformer;
use Auth;
use Dingo\Api\Http\Request;

class ProductController extends Controller
{
    public function all()
    {
        $products = Product::all();
        // 如果是管理员，返回全部数据；否则返回上架数据
        if (Auth::check() && Auth::user()->isAdmin())
            return $products;
        else
            return $this->response->collection($products, new ProductTransformer);
    }

    public function store(Product $product, Request $request)
    {
        // 验证规则 也是接收的字段
        $rules = collect([
            'name' => 'required|string',
            'introduction' => 'required|string',
            'price' => 'required|numeric',
            'show' => 'required|boolean',
            'required_params' => 'required|json',
            'category_id' => 'required|exists:categories,id'
        ]);

        $data = $this->validate($request, $rules->toArray());
        $data['price'] = $data['price'] * 100;

        // 保存数据
        if ($product->fill($data)->save())
            $this->response->created();
        else
            $this->response->errorInternal();
    }


    public function delete(Product $product)
    {
        if (!$product->exists) {
            $this->response->errorNotFound('产品不存在');
            return;
        }

        if ($product->orders->count() > 0) {
            $this->response->errorForbidden('请先删除该商品下的所有订单');
            return;
        }

        if ($product->delete()) {
            return ['message' => '已删除'];
        } else {
            $this->response->errorInternal('删除失败');
        }
    }

    public function buy(Product $product, Request $request)
    {
        if (!$product->exists) {
            $this->response->errorNotFound('产品不存在');
        }
        $stock = $product->stock === -1 ? 99999 : $product->stock;

        //TODO: 入库前对用户输入数据转义
        $data = $this->validate($request, [
            'number' => 'required|integer|min:1|max:' . $stock,
            'params' => 'required|json' //NOTICE: 用户输入数据，输出时格外注意
        ]);

        $params = json_decode($data['params']);

        // 创建订单
        $result = app('App\Creators\OrderCreator')->create($product, $data['number'], $params);

        if ($request) {
            return ['order_id' => $result->id];
        } else {
            $this->response->errorInternal('创建失败');
        }
    }

}