<?php

namespace App\Http\Controllers;

use App\Rate;
use DateTime;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->updateRates();
    }


    /**
     * Update rates if a day passed
     *      
     * @return void
     */
    private function updateRates()
    {
        if (session('rate') != null) return;
        dump(session());
        $rate = Rate::where('created_at', '>=', new DateTime('today'))->first();
        if ($rate === null) {
            dump('creating rate');
            $rate = new Rate();
            $rate->updateRates();
            session(['rate' => $rate->eurtousd]);
        }
    }
}
