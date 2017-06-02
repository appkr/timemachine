<?php

namespace Appkr\Timemachine;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class GetTimeDiffRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'target_time' => 'date_format:Y-m-d H:i:s',
        ];
    }

    public function getTargetTime()
    {
        return new Carbon($this->input('target_server_time', 'now'));
    }
}