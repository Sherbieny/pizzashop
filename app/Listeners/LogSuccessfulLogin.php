<?php

namespace App\Listeners;

use App\Cart;
use App\Item;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * 
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $this->resolveCart($user);
    }

    /**
     * Resolve cart logic
     * This is needed only to merge carts all other cases are covered in CartController::getCart()      
     * 1) Guest cart is not empty && User has old active cart - merge carts     
     *      
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * 
     * @return void
     */
    private function resolveCart($user)
    {
        Log::info(__METHOD__);
        //Get active guest cart id from session if it exists
        $guestCartId = (int) session('cart_id');
        //if no guest cart found, return
        if ($guestCartId == 0) return;
        //Get guest cart
        $guestCart = Cart::findOrFail($guestCartId);
        //Get old active cart if it exists
        $oldCart = Cart::where([
            ['customer_id', '=', $user->id],
            ['is_active', '=', true]
        ])->first();
        //if user has no old active cart, return
        if ($oldCart === null) return;

        //Merge carts
        $newItems = $guestCart->getItems();
        //if guest cart is empty, remove it from session to load old cart on next add to cart
        if (empty($newItems)) {
            Log::info('cart is empty');
            session(['cart_id' => null]);
            return;
        }
        Log::info('1');
        foreach ($newItems as $newItem) {
            //get or create item and add product
            $item = Item::firstOrCreate([
                'product_id' => $newItem->product_id,
                'cart_id' => $oldCart->id
            ]);
            Log::info('item found or created');
            //add or update qty and cost        
            $item->qty = (int) $item->qty + 1;
            $item->cost = $item->qty * $item->product()->price;

            //save item
            $item->save();
        }

        Log::info('saving cart');
        //collect totals and save
        $oldCart->collectTotals()->save();
        //delete guest cart
        $guestCart->delete();
        //add old cart id to session
        session(['cart_id' => $oldCart->id]);
    }
}
