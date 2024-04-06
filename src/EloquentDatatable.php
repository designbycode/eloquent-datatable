<?php

namespace Designbycode\EloquentDatatable;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait EloquentDatatable
{
    protected bool $allowDeletion = true;

    protected bool $allowCreation = true;

    protected bool $createUsingDialog = false;

    protected bool $allowSearching = true;

    protected int $defaultLimit = 25;

    protected string $sortDirection = 'desc';

    public Builder $builder;

    public function __construct()
    {
        $this->builder = $this->builder();
    }

    /**
     * Use Model::query() image model controller
     */
    abstract public function builder(): Builder;

    /**
     * Get all response data to be added to index
     *
     * @return array[]
     *
     * @throws Exception
     */
    public function getResponse(Request $request): array
    {
        return [
            'data' => [
                'meta' => [
                    'name' => $this->getDataTableName(),
                    'name_singular' => Str::singular($this->getDataTableName()),
                    'allow' => [
                        'deletions' => $this->allowDeletion,
                        'creation' => $this->allowCreation,
                        'searching' => $this->allowSearching,
                        'create_with_dialog' => $this->createUsingDialog,
                    ],
                    'pagination_limit' => $this->getLimit($request),
                ],
                'database' => [
                    'typings' => $this->getModelDatabaseTypings(),
                ],
                'columns' => [
                    'updatable' => $this->getUpdatableColumns(),
                    'creatable' => $this->getCreatableColumns(),
                    'displayable' => $this->getDisplayableColumns(),
                    'quick_create' => $this->getQuickCreateColumns(),
                ],
                'records' => $this->getRecords($request),
            ],
        ];
    }

    /**
     * Get the name of the database table
     */
    public function getDataTableName(): string
    {
        return $this->builder->getModel()->getTable();
    }

    /**
     * Get the paginated limit
     */
    private function getLimit(Request $request): int
    {
        return $request->limit ?? $this->defaultLimit;
    }

    /**
     * They will give the typing of the database field
     */
    private function getModelDatabaseTypings(): array
    {
        return array_intersect_key($this->getTableColumns(), array_flip(array_values($this->getCreatableColumns())));
    }

    /**
     * Get array of database columns
     */
    protected function getTableColumns(): array
    {
        $table = $this->builder->getModel()->getTable();
        $builder = $this->builder->getModel()->getConnection()->getSchemaBuilder();
        $columns = $builder->getColumnListing($table);
        $columnsWithType = collect($columns)->mapWithKeys(function ($item) use ($builder, $table) {
            $key = $builder->getColumnType($table, $item);

            return [$item => $key];
        });

        return $columnsWithType->toArray();
    }

    /**
     * Give array of fields that is fillable
     */
    protected function getCreatableColumns(): array
    {
        return $this->builder->getModel()->getFillable();
    }

    /**
     * Only column field that can be updated
     */
    protected function getUpdatableColumns(): array
    {
        return $this->getDisplayableColumns();
    }

    /**
     * Quick create columns
     */
    protected function getQuickCreateColumns(): array
    {
        return $this->getCreatableColumns();
    }

    /**
     * Give array of database columns
     */
    private function getDisplayableColumns(): array
    {
        return array_diff($this->getDisplayableColumnNames(), $this->builder->getModel()->getHidden(), $this->builder->getModel()->getDates());
    }

    /**
     * Gives the names of database column
     */
    protected function getDisplayableColumnNames(): array
    {
        return Schema::getColumnListing($this->builder->getModel()->getTable());
    }

    /**
     * Build query and return collection of data
     *
     * @throws Exception
     */
    private function getRecords(Request $request): LengthAwarePaginator
    {
        $builder = $this->builder;

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        return $this->builder->orderBy('id', $this->sortDirection)->paginate($this->getLimit($request))
            ->through(function ($model) {
                return Arr::only($model->toArray(), $this->getDisplayableColumns());
            });
    }

    protected function itemStore(array $array): void
    {
        $this->builder->create($array);
    }

    protected function itemUpdate(int $id, array $array): void
    {
        $this->builder->findOrFail($id)->update($array);
    }

    /**
     * @return bool|mixed|void|null
     */
    protected function itemDelete(int $id)
    {
        if ($this->allowDeletion) {
            return $this->builder->find($id)->delete();
        }
    }

    /**
     * @return mixed|void
     */
    protected function itemsDelete(string $ids)
    {
        if ($this->allowDeletion) {
            return $this->builder->whereIn('id', explode(',', $ids))->delete();
        }
    }

    /**
     * Check if search query is present
     */
    protected function hasSearchQuery(Request $request): bool
    {
        return count(array_filter($request->only('column', 'operator', 'value'))) === 3;
    }

    /**
     * @throws Exception
     */
    private function buildSearch(Builder $builder, Request $request): Builder
    {
        $queryParts = $this->resolveQueryParts(operator: $request->operator, value: $request->value);
        if (! $this->hasSearchQuery($request)) {
            throw new Exception('No search query provided');
        }

        return $builder->where(column: $request->column, operator: $queryParts['operator'], value: $queryParts['value']);
    }

    private function resolveQueryParts(string $operator, string $value): array
    {
        return Arr::get([
            'equals' => [
                'operator' => '=',
                'value' => $value,
            ],
            'contains' => [
                'operator' => 'LIKE',
                'value' => "%$value%",
            ],
            'starts_with' => [
                'operator' => 'LIKE',
                'value' => "$value%",
            ],
            'ends_with' => [
                'operator' => 'LIKE',
                'value' => "%$value",
            ],
            'greater_than' => [
                'operator' => '>',
                'value' => $value,
            ],
            'greater_or_equal_than' => [
                'operator' => '>=',
                'value' => $value,
            ],
            'less_than' => [
                'operator' => '<',
                'value' => $value,
            ],
            'less_or_equal_than' => [
                'operator' => '<=',
                'value' => $value,
            ],
        ], $operator);
    }
}
