<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiSuccessResponse implements Responsable
{
    public function __construct(private readonly array $data,
                                private readonly array $meta = [],
                                private readonly int   $status = 200,
                                private readonly array $headers = [])
    {
    }

    public function toResponse($request): JsonResponse|Response
    {
        return response()->json([
            'data' => $this->data,
            'meta' => $this->meta,
        ], $this->status, $this->headers);
    }
}
