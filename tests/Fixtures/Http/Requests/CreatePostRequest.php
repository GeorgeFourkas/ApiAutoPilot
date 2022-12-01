<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'body' => 'required',
            'title' => 'required',
            'featured_image_url' => 'required'
        ];
    }

}
