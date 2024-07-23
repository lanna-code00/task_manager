<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UnauthorizedException extends Exception
{
    public function render(Request $request)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized'
        ], 403);
    }
}
