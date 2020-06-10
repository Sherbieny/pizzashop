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
        $items = Item::where('cart_id', $this->id)->get();

        if (empty($items)) $this;

        foreach ($items as $item) {
            $qty += $item->qty;
            $total += $item->cost;
        }

        $this->qty = $qty;
        $this->total = $total;

        return $this;
    }
}
