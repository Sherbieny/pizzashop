<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Item;
use App\Product;
use App\Rate;
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
        if (Auth::guest() === false) {
            $carts = Cart::where([
                ['customer_id', '=', Auth::user()->id],
                ['is_active', '=', false]
            ])->orderBy('updated_at', 'desc')->paginate(10);
            $rate = Rate::latest()->first();
            return view('cart.index', [
                'carts' => $carts,
                'rate' => $rate->eurtousd
            ]);
        } else {
            return redirect()->route('product')->with('error', 'You have no access to order history .. please register');
        }
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
        //Get cart
        $cart = $this->getCart();

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
        session(['item_count' => $cart->qty]);

        return back()->with('success', 'Product added to cart');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $cartId
     * @return \Illuminate\Http\Response
     */
    public function show($cartId)
    {
        $cart = Cart::findOrFail($cartId);
        $rate = Rate::latest()->first();
        $usdTotal = (float) $rate->eurtousd * (float) $cart->total;
        $total = '€ ' . number_format($cart->total, 2) . ' |  $ ' . number_format($usdTotal, 2);
        return view('cart.show', [
            'cart' => $cart,
            'total' => $total
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $itemId
     * @return \Illuminate\Http\Response
     */
    public function remove($itemId)
    {
        //Get the cart
        $cart = $this->getCart();
        //get item
        $item = Item::findOrFail($itemId);
        //delete item
        $item->delete();
        //collect totals and save
        $cart->collectTotals()->save();

        //update session cart item count to view in frontend        
        session(['item_count' => $cart->qty]);

        return back()->with('success', 'Product removed from cart');
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
        $this->validate($request, [
            'email' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required'
        ]);

        $cart->customer_email = $request->input('email');
        $cart->customer_firstname = $request->input('email');
        $cart->customer_lastname = $request->input('first_name');
        $cart->address = $request->input('address');
        //disable cart so it wont get called again after order is placed
        $cart->is_active = false;
        $cart->save();
        //remove current cart from session to create new one for new requests
        session(['cart_id' => null]);

        return redirect()->route('product')->with('success', 'Order placed .. Thank you!');
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

    /**
     * Get current cart or create one
     *      
     * @return Cart
     */
    private function getCart()
    {
        //Get cart id from session if it exists
        $cartId = (int) session('cart_id');
        //Get user if logged in
        $user = Auth::guest() === false ? User::findOrFail(Auth::id()) : null;
        //Get or create cart
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
            //save cart to properly populate it with items
            $cart->save();
            //update session with cart id if new cart is created
            session(['cart_id' => $cart->id]);
        } elseif ($newCart && !$user) {
            //save cart to properly populate it with items
            $cart->save();
            //update session with cart id if new cart is created
            session(['cart_id' => $cart->id]);
        }

        return $cart;
    }
}
