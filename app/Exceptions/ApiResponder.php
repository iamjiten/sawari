<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

trait ApiResponder
{
    /**
     * Return a success JSON response.
     *
     * @param array|string|null $data
     * @param string $message
     * @param int|null $code
     * @return JsonResponse
     */
    protected function success($data = null, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $code
     * @param array|string|null $data
     * @return JsonResponse
     */
    protected function error(string $message = null, int $code, $data = null): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function somethingWentWrong(Exception $exception = null, string $message = 'Something went wrong'): JsonResponse
    {
        DB::rollBack();
        Log::error($exception);
        return response()->json([
            'message' => $message,
            'status' => 500,
            'error_message' => $exception->getMessage()
        ], 500);
    }

}
