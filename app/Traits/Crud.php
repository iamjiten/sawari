<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Crud
{
    public static function initializer($initializeModel = 'initializeModel')
    {
        $request = request();
        $sortBy = $request->sortBy;
        $desc = $request->descending === "true";
        $query = $request->get('query');
        $filters = json_decode($request->query('filters', ""), true);
        $filters = $filters ? array_merge($filters, $request->query()) : $request->query();
        if (method_exists(static::class, $initializeModel)) {
            $model = static::$initializeModel();
        } else {
            $model = static::query();
        }

        /** If the model has any query then filter by query */
        if ($query) $model->queryfilter($query);
        foreach ($filters as $filter => $value) {
            if ($value !== null) {
                $method = ucfirst(Str::camel($filter));
                if (method_exists(static::class, 'scope' . $method)) {
                    $model->{$method}($value);
                } elseif (method_exists($model, $filter)) {
                    $model->{$filter}($value);
                }
            }
        }

        if ($sortBy && $sortBy !== 'null') {
            return $model->orderBy($sortBy, $desc ? 'desc' : 'asc');
        }

        return $model->latest();
    }

    public static function getTableName(): string
    {
        return (new self())->getTable();
    }
}
