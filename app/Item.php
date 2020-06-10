<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['product_id', 'cart_id'];


    /**
     * Get product
     *      
     * @return Product|null
     */
    public function product()
    {
        return $this->hasOne('App\Product');
    }

    /**
     * Get cart
     *      
     * @return Cart|null
     */
    public function cart()
    {
        return $this->belongsTo('App\Cart');
    }
}
