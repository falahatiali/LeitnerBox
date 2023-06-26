<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiErrorResponse implements Responsable
{
    public function __construct(private string $message = '', private int $status = 400, private ?Throwable $exception = null)
    {
    }

    public function toResponse($request): JsonResponse|Response
    {
        $response['message'] = $this->message;

        if ($this->exception && config('app.debug')) {
            $response['debug'] = [
                'exception' => [
                    'message' => $this->exception->getMessage(),
                    'code' => $this->exception->getCode(),
                    'file' => $this->exception->getFile(),
                    'line' => $this->exception->getLine(),
                ]
            ];
        }

        return response()->json([
            $response
        ], $this->status ?? $this->exception->getCode());
    }
}
