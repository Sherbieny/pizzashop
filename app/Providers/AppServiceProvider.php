<?php

namespace App\Providers;

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

        Blade::directive('dollar', function ($amount) {
            $exchangeRates = new ExchangeRate();
            $value = $exchangeRates->convert((int) $amount, 'EUR', 'USD');
            return "<?php echo '$' . number_format($value, 2); ?>";
        });
        Blade::directive('euro', function ($amount) {
            return "<?php echo '€' . number_format($amount, 2); ?>";
        });
    }
}
