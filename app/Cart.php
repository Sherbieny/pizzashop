<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    /**
     * Collect total
     *      
     * @return $this
     */
    public function collectTotals()
    {
        $qty = 0;
        $total = 0;
        $items = $this->getItems();

        if (empty($items)) $this;

        foreach ($items as $item) {
            $qty += $item->qty;
            $total += $item->cost;
        }

        $this->qty = $qty;
        $this->total = $total;

        return $this;
    }

    /**
     * Get cart items
     *      
     * @return Item[]|null
     */
    public function getItems()
    {
        return Item::where('cart_id', $this->id)->get();
    }


    /**
     * Get cart items
     *      
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany('App\Item', 'cart_id', 'id');
    }

    /**
     * Get customer if not a guest
     *      
     * @return User|null
     */
    public function customer()
    {
        return $this->hasOne('App\User');
    }
}
