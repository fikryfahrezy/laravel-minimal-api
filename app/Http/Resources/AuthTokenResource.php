<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'message' => $this['message'],
            'token' => $this['token'],
            'token_type' => $this['token_type'],
            'user' => new UserResource($this['user']),
        ];
    }
}
