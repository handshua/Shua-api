<?php

namespace App\Http\Controllers;

use Dingo\Api\Exception\ValidationHttpException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use Helpers;

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        parent::validate($request, $rules, $messages, $customAttributes);
        return $request->only(array_keys($rules));
    }

    protected function throwValidationException(Request $request, $validator)
    {
        throw new ValidationHttpException($validator->errors()->getMessages());
    }
}
