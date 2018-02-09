<?php

namespace App\Http\Controllers;


use App\Models\Category;
use Auth;
use Dingo\Api\Http\Request;

class CategoryController extends Controller
{
    public function all()
    {
        $columns = ['id', 'name', 'weight'];

        // 如果是管理员，返回全部数据；否则返回上架数据
        if (Auth::check() && Auth::user()->isAdmin())
            return Category::all();
        else
            return Category::whereShow(true)->get($columns);

    }

    public function store(Category $category, Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|string',
            'weight' => 'required|integer|min:0|max:100',
            'show' => 'required|boolean'
        ]);

        if ($category->fill($data)->save())
            $this->response->created();
        else
            $this->response->errorInternal();

    }

    public function delete(Category $category)
    {
        if (!$category->exists) {
            $this->response->errorNotFound('该分类不存在');
            return;
        }

        if ($category->products->count() > 0) {
            $this->response->errorForbidden('请先删除该分类下所有商品');
            return;
        }

        if ($category->delete()) {
            return ['message' => '已删除'];
        } else {
            $this->response->errorInternal('删除失败');
        }
    }

}