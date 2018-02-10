<?php

namespace App\Http\Controllers;


use App\Models\Order;
use App\Models\Product;
use App\Transformers\ProductTransformer;
use Auth;
use Dingo\Api\Exception\ValidationHttpException;
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

    public function store(Request $request, $product = null)
    {
        // Lumen 貌似无法自动注入模型
        $product = Product::findOrNew($product);

        // 验证规则 也是接收的字段
        $rules = collect([
            'name' => 'required|string',
            'introduction' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'show' => 'required|boolean',
            'required_params' => "required|array",
            'category_id' => 'required|integer|exists:categories,id',
//            'warranty_period' => 'required|date', 辣鸡验证器，无法校验 + 30 days 格式的日期
            'warranty_period' => 'required',
        ]);

        $data = $this->validate($request, $rules->toArray());

        // 二次手动校验warranty_period
        if (!strtotime($data['warranty_period']))
            throw new ValidationHttpException();

        $data['price'] = $data['price'] * 100;

        // 保存数据
        if ($product->fill($data)->save())
            $this->response->created();
        else
            $this->response->errorInternal();
    }


    public function delete($product)
    {
        $product = Product::findOrFail($product);

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