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
        $this->updateRates();
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
    private function updateRates()
    {
        $rate = Rate::where('created_at', '>=', new DateTime('today'));
        if ($rate === null) {
            dd('sameer');
        } else {
            dd('moneer');
        }
    }
}
