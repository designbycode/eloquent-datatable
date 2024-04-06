<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('it will use the trait')
    ->expect('Designbycode\EloquentDatatable\EloquentDatatable')
    ->toBeTrait();

arch('it will use the controller that is abstract')
    ->expect('Designbycode\EloquentDatatable\EloquentDatatableController')
    ->toBeAbstract();

arch('it will have service provider')
    ->expect('Designbycode\EloquentDatatable\EloquentDatatableServiceProvider')
    ->toExtend('Spatie\LaravelPackageTools\PackageServiceProvider');
