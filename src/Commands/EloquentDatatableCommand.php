<?php

namespace Designbycode\EloquentDatatable\Commands;

use Illuminate\Console\Command;

class EloquentDatatableCommand extends Command
{
    public $signature = 'eloquent-datatable';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
