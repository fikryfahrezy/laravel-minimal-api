<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Laravel Minimal API',
    description: 'Versioned API with Sanctum token authentication and todo CRUD.'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Use a Sanctum personal access token in the Authorization header.'
)]
#[OA\Schema(
    schema: 'TodoPaginationResponse',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Todo')),
        new OA\Property(
            property: 'meta',
            type: 'object',
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'from', type: 'integer', nullable: true, example: 1),
                new OA\Property(property: 'last_page', type: 'integer', example: 1),
                new OA\Property(property: 'path', type: 'string', example: 'http://localhost/api/v1/todos'),
                new OA\Property(property: 'per_page', type: 'integer', example: 15),
                new OA\Property(property: 'to', type: 'integer', nullable: true, example: 15),
                new OA\Property(property: 'total', type: 'integer', example: 1),
            ]
        ),
        new OA\Property(
            property: 'links',
            type: 'object',
            properties: [
                new OA\Property(property: 'first', type: 'string', nullable: true),
                new OA\Property(property: 'last', type: 'string', nullable: true),
                new OA\Property(property: 'prev', type: 'string', nullable: true),
                new OA\Property(property: 'next', type: 'string', nullable: true),
            ]
        ),
    ],
    type: 'object'
)]
#[OA\Tag(name: 'Authentication', description: 'User registration and login endpoints')]
#[OA\Tag(name: 'Todos', description: 'Authenticated todo management endpoints')]
class OpenApiSpec {}
