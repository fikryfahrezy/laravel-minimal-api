<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TodoCollection extends ResourceCollection
{
    public $collects = TodoResource::class;

    public function toArray(Request $request): array
    {
        return $this->collection->all();
    }

    public function paginationInformation($request, $paginated, $default): array
    {
        return [
            'links' => $default['links'],
            'meta' => $default['meta'],
        ];
    }
}
