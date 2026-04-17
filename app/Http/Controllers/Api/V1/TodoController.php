<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Todo\IndexTodoRequest;
use App\Http\Requests\Todo\StoreTodoRequest;
use App\Http\Requests\Todo\UpdateTodoRequest;
use App\Http\Resources\TodoCollection;
use App\Http\Resources\TodoResource;
use App\Services\Todo\TodoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TodoController extends Controller
{
    public function __construct(private readonly TodoService $todoService) {}

    #[OA\Get(
        path: '/api/v1/todos',
        tags: ['Todos'],
        summary: 'List todos for the authenticated user',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'is_completed', in: 'query', required: false, schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['created_at', 'title', 'is_completed'])),
            new OA\Parameter(name: 'sort_direction', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Todo list',
                content: new OA\JsonContent(ref: '#/components/schemas/TodoPaginationResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(IndexTodoRequest $request): TodoCollection
    {
        return new TodoCollection($this->todoService->list($request->user(), $request->validated()));
    }

    #[OA\Post(
        path: '/api/v1/todos',
        tags: ['Todos'],
        summary: 'Create a todo',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Write docs'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Draft the API documentation'),
                    new OA\Property(property: 'is_completed', type: 'boolean', example: false),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Todo created', content: new OA\JsonContent(ref: '#/components/schemas/Todo')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(StoreTodoRequest $request): JsonResponse
    {
        return (new TodoResource($this->todoService->create($request->user(), $request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/v1/todos/{id}',
        tags: ['Todos'],
        summary: 'Show a single todo',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Todo details', content: new OA\JsonContent(ref: '#/components/schemas/Todo')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Todo not found'),
        ]
    )]
    public function show(Request $request, int $todo): TodoResource
    {
        return new TodoResource($this->todoService->show($request->user(), $todo));
    }

    #[OA\Put(
        path: '/api/v1/todos/{id}',
        tags: ['Todos'],
        summary: 'Update a todo',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Write more tests'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Cover auth and todos'),
                    new OA\Property(property: 'is_completed', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Todo updated', content: new OA\JsonContent(ref: '#/components/schemas/Todo')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Todo not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(UpdateTodoRequest $request, int $todo): TodoResource
    {
        return new TodoResource($this->todoService->update($request->user(), $todo, $request->validated()));
    }

    #[OA\Delete(
        path: '/api/v1/todos/{id}',
        tags: ['Todos'],
        summary: 'Delete a todo',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Todo deleted'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Todo not found'),
        ]
    )]
    public function destroy(Request $request, int $todo): JsonResponse
    {
        $this->todoService->delete($request->user(), $todo);

        return response()->json(status: 204);
    }
}
