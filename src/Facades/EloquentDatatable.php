<?php

namespace Designbycode\EloquentDatatable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Designbycode\EloquentDatatable\EloquentDatatable
 */
class EloquentDatatable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Designbycode\EloquentDatatable\EloquentDatatable::class;
    }
}
