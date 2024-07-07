<?php

namespace App\Exceptions;

use Exception;

class TaskNotFoundException extends Exception
{
    public function render($request)
    {
        return response()->json(['error' => 'Задача не была найдена'], 404);
    }
}
