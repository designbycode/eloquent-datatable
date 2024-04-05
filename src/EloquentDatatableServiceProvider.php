<?php

namespace Designbycode\EloquentDatatable;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Designbycode\EloquentDatatable\Commands\EloquentDatatableCommand;

class EloquentDatatableServiceProvider extends PackageServiceProvider
{
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
            ->hasViews()
            ->hasMigration('create_eloquent-datatable_table')
            ->hasCommand(EloquentDatatableCommand::class);
    }
}
