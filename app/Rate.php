<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{

    /**
     * Update todays rate
     *      
     * @return $this
     */
    public function updateRates()
    {
        $apikey = 'a53e0c9f064ea01da0e2';

        $from_Currency = urlencode('EUR');
        $to_Currency = urlencode('USD');
        $query =  "{$from_Currency}_{$to_Currency}";

        // change to the free URL if you're using the free version
        $json = file_get_contents("https://free.currconv.com/api/v7/convert?q={$query}&compact=ultra&apiKey={$apikey}");
        $obj = json_decode($json, true);

        $rate = floatval($obj["$query"]);

        $this->eurtousd = $rate;

        $this->save();
    }
}
