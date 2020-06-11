<?php

namespace App\Providers;

use App\Rate;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        URL::forceScheme('https');
        Schema::defaultStringLength(191);

        Blade::directive('money', function ($amount) {
            $rate = Rate::latest()->first();
            dump($rate->eurtousd);
            dump($amount);
            $usdAmount = (float) $amount * ((float) $rate->eurtousd);
            return "<?php echo '€' . number_format($amount, 2) . ' | $' . number_format($usdAmount, 2); ?>";
        });
    }
}
