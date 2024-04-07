<?php

namespace Designbycode\EloquentDatatable;

use Designbycode\EloquentDatatable\Commands\EloquentDatatableCommand;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EloquentDatatableServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        parent::boot();
        Route::macro('datatable', function ($url, $controller) {
            Route::resource($url, $controller)->except('show', 'create');
        });
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('eloquent-datatable')
            ->hasConfigFile()
            ->hasCommand(EloquentDatatableCommand::class);

        //            ->hasViewComponents('Datatable')
        //            ->hasViews()
        //            ->hasMigration('create_eloquent-datatable_table')
        //            ->hasCommand(EloquentDatatableCommand::class);
    }
}
