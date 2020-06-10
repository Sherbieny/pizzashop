<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Item;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Add product to cart item and cart item to cart
     *
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function add($productId)
    {
        //Get the product
        $product = Product::findOrFail($productId);
        //Get user if logged in
        $user = Auth::guest() === false ? User::findOrFail(Auth::id()) : null;
        //Get or create cart
        $cartId = (int) Session::get('cart_id');
        $cart = ($cartId > 0) ? Cart::findOrFail($cartId) : new Cart();
        //determine if cart is new
        $newCart = $cart->id === null;
        //if new cart, and user is logged in add user info and update session
        if ($newCart && $user) {
            $cart->customer_id = $user->id;
            $cart->customer_email = $user->email;
            $customerNames = explode(' ', $user->name) !== false ? explode(' ', $user->name) : [];
            $cart->customer_firstname = !empty($customerNames) ? $customerNames[0] : null;
            $cart->customer_lastname = count($customerNames) > 1 ? $customerNames[1] : null;

            //save cart to properly populate it with item below
            $cart->save();

            //update session with cart id if new cart is created
            Session::push('cart_id', $cart->id);
        }


        //get or create item and add product
        $item = Item::firstOrCreate([
            'product_id' => $productId,
            'cart_id' => $cart->id
        ]);

        //add or update qty and cost        
        $item->qty = (int) $item->qty + 1;
        $item->cost = $item->qty * $product->price;

        //save item
        $item->save();

        //collect totals and save
        $cart->collectTotals()->save();

        //update session cart item count to view in frontend
        Session::push('item_count', $cart->qty);

        return back()->with('success', 'Product added to cart');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
