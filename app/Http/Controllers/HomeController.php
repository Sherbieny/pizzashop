<?php

namespace App\Http\Controllers;

use App\Rate;
use DateTime;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Update rates if a day passed
     *      
     * @return void
     */
    public static function updateRates()
    {
        $rate = Rate::where('created_at', '>=', new DateTime('today'))->first();
        if ($rate === null) {
            $rate = new Rate();
            $rate->updateRates();
        }
    }
}
