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
        Log::info('getting guest cart id from session = ' . $guestCartId);
        //if no guest cart found, return
        if ($guestCartId == 0) return;
        //Get guest cart
        $guestCart = Cart::findOrFail($guestCartId);
        Log::info('guest cart loaded');
        //Get old active cart if it exists
        $oldCart = Cart::where([
            ['customer_id', '=', $user->id],
            ['is_active', '=', true]
        ])->first();
        Log::info('loading old cart');
        //if user has no old active cart, set customer info and return
        if ($oldCart === null) {
            $guestCart->customer_id = $user->id;
            $guestCart->customer_email = $user->email;
            $customerNames = explode(' ', $user->name) !== false ? explode(' ', $user->name) : [];
            $guestCart->customer_firstname = !empty($customerNames) ? $customerNames[0] : null;
            $guestCart->customer_lastname = count($customerNames) > 1 ? $customerNames[1] : null;
            //save cart to properly populate it with items
            $guestCart->save();
            return;
        } else {
            Log::info('old cart exists');
            //if old cart exists, it should be assigned to session if it has items
            if ($oldCart->qty > 0) {
                Log::info('setting old cart data');
                session(['cart_id' => $oldCart->id]);
                session(['item_count' => $oldCart->qty]);
            }
        }
        Log::info('old cart loaded');

        //Merge carts
        $newItems = $guestCart->items;
        //if guest cart is empty, remove it from session to load old cart on next add to cart
        if (empty($newItems)) {
            Log::info('guest cart is empty');
            session(['cart_id' => null]);
            return;
        }
        Log::info('guest cart is not empty');
        foreach ($newItems as $newItem) {
            //get or create item and add product
            $item = Item::firstOrCreate([
                'product_id' => $newItem->product_id,
                'cart_id' => $oldCart->id
            ]);
            Log::info('item found or created');
            //add or update qty and cost        
            $item->qty = (int) $item->qty + 1;
            $item->cost = $item->qty * $item->product->price;

            //save item
            $item->save();
        }

        Log::info('saving cart');
        //collect totals and save
        $oldCart->collectTotals()->save();
        //delete guest cart
        Log::info('old cart saved');
        $guestCart->delete();
        Log::info('guest cart deleted');
        //add old cart id to session
        session(['cart_id' => $oldCart->id]);
        //update cart count
        session(['item_count' => $oldCart->qty]);
        Log::info('session updated');
    }
}
