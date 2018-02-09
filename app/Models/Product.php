<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'introduction', 'price', 'category_id', 'required_params'];

    public function category()
    {
        return $this->belongsTo(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function setPriceAttribute($price)
    {
        if (strpos((string)$price, '.') !== false)
            $price *= 100;

        $this->attributes['price'] = $price;
    }

    public function setRequiredParamsAttribute(Array $value)
    {
        $this->required_params = json_encode($value);
    }

    public function getRequiredParamsAttribute()
    {
        return json_decode($this->required_params);
    }
}