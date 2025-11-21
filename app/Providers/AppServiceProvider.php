<?php



namespace App\Providers;



use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\Events\WorkerStopping;



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
    Event::listen(WorkerStopping::class, function () {
        DB::disconnect('mysql');
    });
            Paginator::useBootstrap();

}

   /* public function boot()

    {

        Paginator::useBootstrap();

    }*/

}

