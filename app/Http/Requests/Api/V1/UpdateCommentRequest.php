<?php

namespace App\Http\Requests\Api\V1;

class UpdateCommentRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'max:1000'],
        ];
    }
}
