<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Item;
use App\Product;
use App\Rate;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        try {
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
        } catch (\Exception $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('product')->with('error', 'Failed to add product');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $cartId
     * @return \Illuminate\Http\Response
     */
    public function show($cartId)
    {
        try {
            $cart = Cart::findOrFail($cartId);

            if (Auth::guest() === false) {
                //check if user has access to cart
                if (Auth::id() != $cart->customer_id) {
                    return redirect()->route('product')->with('error', 'You have no access to this cart history');
                }
            } else {
                //if not logged in, check if cart is the active cart in session
                if ($cart->is_active === false) {
                    return redirect()->route('product')->with('error', 'You have no access to order history .. please register');
                }
            }

            $rate = Rate::latest()->first();
            $usdTotal = (float) $rate->eurtousd * (float) $cart->total;
            $total = '€ ' . number_format($cart->total, 2) . ' |  $ ' . number_format($usdTotal, 2);
            $delivery = '€ ' . number_format(2, 2) . ' |  $ ' . number_format(2 * (float) $rate->eurtousd, 2);
            return view('cart.show', [
                'cart' => $cart,
                'total' => $total,
                'delivery' => $delivery
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('product')->with('error', 'The was a problem in opening order history');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $itemId
     * @return \Illuminate\Http\Response
     */
    public function remove($itemId)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('product')->with('error', 'Failed to remove product');
        }
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

        try {
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
        } catch (\Exception $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('product')->with('error', 'Failed to update cart');
        }
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
        if ($cartId > 0) {
            $cart = Cart::findOrFail($cartId);
        } else {
            //Get old active cart if it exists, else create new
            if ($user) {
                $cart = Cart::where([
                    ['customer_id', '=', $user->id],
                    ['is_active', '=', true]
                ])->first();
                if ($cart === null) {
                    $cart = new Cart();
                }
            }
            //If guest and not cart in session, create new
            else {
                $cart = new Cart();
            }
        }
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
        } elseif ($newCart && !$user) {
            //save cart to properly populate it with items
            $cart->save();
        }

        //update session with cart id if it does not exist
        if ($cartId == 0) session(['cart_id' => $cart->id]);

        return $cart;
    }
}
