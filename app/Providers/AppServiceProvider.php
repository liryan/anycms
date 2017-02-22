<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('require_once', function($string){
            static $includeFiles=[];
            if(!in_array($string,$includeFiles)){
                $includeFiles[]=$string;
                return "<?php echo $string; ?>";
            }
        });

        Blade::directive('import_const',function($string){
            return "<?php use ".with($string)." ?>";
        });

        Blade::directive('block',function($string){
            return "<?php '$block_'".with($string)."=<<<EOF ?>";
        });
        
        Blade::directive('blockend',function(){
            return '<?php echo "\nEOF;"  ?>';
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
