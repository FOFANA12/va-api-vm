<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class DomainException extends Exception
{
    protected int $status;
    protected string $type;

    public function __construct(string $message, int $status = 409, string $type = "business")
    {
        parent::__construct($message);
        $this->status = $status;
        $this->type = $type;
    }

    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'type' => $this->type,
        ], $this->status);
    }
}
