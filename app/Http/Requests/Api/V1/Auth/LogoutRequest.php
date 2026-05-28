<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Http\Requests\Api\V1\BaseApiRequest;

class LogoutRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [];
    }
}
