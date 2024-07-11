<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\BlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;


class
BlogController extends SuperController
{
    public function __construct()
    {
        parent::__construct(
            Blog::class,
            BlogResource::class,
            BlogRequest::class,
            BlogRequest::class
        );
    }
}
