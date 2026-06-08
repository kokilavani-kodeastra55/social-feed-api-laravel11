<?php

namespace App\Http\Requests\Api\V1;

class StorePostRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'sorting_order' => ['nullable', 'integer'],
        ];
    }
}
