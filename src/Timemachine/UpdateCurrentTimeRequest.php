<?php

namespace Appkr\Timemachine;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrentTimeRequest extends FormRequest
{
    const DEFAULT_TIMEMACHINE_TTL = 5;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'add_days' => [
                'required_without_all:add_minutes,sub_days,sub_minutes',
                'integer',
                'between:0,365',
            ],
            'sub_days' => [
                'required_without_all:add_days,add_minutes,sub_minutes',
                'integer',
                'between:0,365',
            ],
            'add_minutes' => [
                'required_without_all:add_days,sub_days,sub_minutes',
                'integer',
                'between:0,1440',
            ],
            'sub_minutes' => [
                'required_without_all:add_days,add_minutes,sub_days',
                'integer',
                'between:0,1440',
            ],
            'ttl' => [
                'integer',
                'between:1,60',
            ],
        ];
    }

    public function getTimemachineSettingsDto()
    {
        $timemachineSettings = new TimemachineSettings;
        $timemachineSettings->addDays = (int) $this->input('add_days', 0);
        $timemachineSettings->subDays = (int) $this->input('sub_days', 0);
        $timemachineSettings->addMinutes = (int) $this->input('add_minutes', 0);
        $timemachineSettings->subMinutes = (int) $this->input('sub_minutes', 0);

        return $timemachineSettings;
    }

    public function getTimemachineSettingsTtl()
    {
        return (int) $this->input('ttl', self::DEFAULT_TIMEMACHINE_TTL);
    }
}