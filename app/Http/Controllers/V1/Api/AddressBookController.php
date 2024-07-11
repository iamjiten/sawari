<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\AddressBookRequest;
use App\Http\Resources\AddressBookResource;
use App\Models\AddressBook;
use Illuminate\Http\Resources\Json\JsonResource;


class AddressBookController extends SuperController
{
    public function __construct()
    {
        parent::__construct(
            AddressBook::class,
            AddressBookResource::class,
            AddressBookRequest::class,
            AddressBookRequest::class
        );
    }
    public function index(): JsonResource
    {
        $addressBook = AddressBook::query()
            ->self();
        return AddressBookResource::collection($addressBook->paginates());
    }

}
