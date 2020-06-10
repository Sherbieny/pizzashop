<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    /**
     * Add item to cart
     * 
     * @param Item $item
     * @return $this
     */
    public function addItem(Item $item)
    {
        //increment current qty
        $this->qty += 1;
        //update total cost
        $this->total = $item->cost;
        //persist data
        $this->save();

        return $this;
    }
}
