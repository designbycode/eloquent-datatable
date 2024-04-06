<?php

namespace Designbycode\EloquentDatatable\Tests;

use Designbycode\EloquentDatatable\EloquentDatatableServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        //        Factory::guessFactoryNamesUsing(
        //            fn (string $modelName) => 'Designbycode\\EloquentDatatable\\Database\\Factories\\'.class_basename($modelName).'Factory'
        //        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            EloquentDatatableServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_eloquent-datatable_table.php.stub';
        $migration->up();
        */
    }
}
