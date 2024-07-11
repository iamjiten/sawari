<?php

namespace App\Providers;

use AshAllenDesign\ShortURL\Classes\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class QueryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    public function boot()
    {
        \Illuminate\Database\Eloquent\Builder::macro('paginates', function (int $perPage = null, $columns = ['*'], $pageName = 'page', int $page = null) {
            request()->validate(['rowsPerPage' => 'nullable|numeric|gte:0|lte:1000000000000000000']);

            $page = $page ?: Paginator::resolveCurrentPage($pageName);

            $total = $this->toBase()->getCountForPagination();

            if ($perPage === null) {
                $rows = (int)request()->query('rowsPerPage', 20);
                if ($rows === 0) {
                    $perPage = $total;
                } else {
                    $perPage = $rows;
                }
            }
            $results = $total
                ? $this->forPage($page, $perPage)->get($columns)
                : $this->model->newCollection();

            return $this->paginator($results, $total, $perPage, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]);
        });

        \Illuminate\Database\Eloquent\Builder::macro('simplePaginates', function (int $perPage = null, $columns = ['*'], $pageName = 'page', $page = null) {
            request()->validate(['rowsPerPage' => 'nullable|numeric|gte:0|lte:1000000000000000000']);
            $page = $page ?: Paginator::resolveCurrentPage($pageName);

            if ($perPage === null) {
                $rows = (int)request()->query('rowsPerPage', 20);
                if ($rows === 0) {
                    $perPage = $this->count();
                } else {
                    $perPage = $rows;
                }
            }

            $this->offset(($page - 1) * $perPage)->limit($perPage + 1);

            return $this->simplePaginator($this->get($columns), $perPage, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]);
        });
    }
}
