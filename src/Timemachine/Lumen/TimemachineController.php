<?php

namespace Appkr\Timemachine\Lumen;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimemachineController extends Controller
{
    const DEFAULT_TIMEMACHINE_TTL = 5;
    const TIMEMACHINE_SETTINGS_CACHE_KEY = 'timemachine.settings';

    public function getTimeDiff(Request $request)
    {
        $this->validateGetTimeDiffReqeust($request);

        $targetTime = $this->getTargetTime($request);
        $now = Carbon::now();
        $isFutureDate = $targetTime >= $now;

        $diffInDays = $now->diffInDays($targetTime);
        $diffInMinutes = $now->diffInMinutes($targetTime) - 60 * 24 * $diffInDays;

        return response()->json([
            'current_server_time' => $now->toIso8601String(),
            'target_server_time' => $targetTime->toIso8601String(),
            'add_days' => $isFutureDate ? $diffInDays : null,
            'add_minutes' => $isFutureDate ? $diffInMinutes : null,
            'sub_days' => !$isFutureDate ? $diffInDays : null,
            'sub_minutes' => !$isFutureDate ? $diffInMinutes : null,
        ]);
    }

    public function setCurrentTime(Request $request)
    {
        $this->validateUpdateCurrentTimeRequest($request);

        /** @var \Illuminate\Contracts\Cache\Repository $cacheRepository */
        $cacheRepository = app('cache.store');
        $timemachineSettingsTtl = $this->getTimemachineSettingsTtl($request);

        $cacheRepository->put(
            self::TIMEMACHINE_SETTINGS_CACHE_KEY,
            $this->getTimemachineSettingsDto($request),
            $timemachineSettingsTtl
        );

        return response()->json([
            'current_server_time' => Carbon::now()->toIso8601String(),
            'message' => "Success. The settings will be effective from next request on for {$timemachineSettingsTtl} minutes.",
        ]);
    }

    public function resetCurrentTime()
    {
        /** @var \Illuminate\Contracts\Cache\Repository $cacheRepository */
        $cacheRepository = app('cache.store');
        $cacheRepository->forget(self::TIMEMACHINE_SETTINGS_CACHE_KEY);

        return response()->json([
            'current_server_time' => Carbon::now()->toIso8601String(),
            'message' => 'Success. Settings removed.',
        ]);
    }


    private function validateGetTimeDiffReqeust(Request $request)
    {
        $this->validate($request, [
            'target_server_time' => 'date_format:Y-m-d H:i:s',
        ]);
    }

    private function getTargetTime(Request $request)
    {
        return new Carbon($request->input('target_server_time', 'now'));
    }

    private function validateUpdateCurrentTimeRequest(Request $request)
    {
        $this->validate($request, [
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
        ]);
    }

    private function getTimemachineSettingsDto(Request $request)
    {
        $timemachineSettings = new TimemachineSettings;
        $timemachineSettings->addDays = (int) $request->input('add_days', 0);
        $timemachineSettings->subDays = (int) $request->input('sub_days', 0);
        $timemachineSettings->addMinutes = (int) $request->input('add_minutes', 0);
        $timemachineSettings->subMinutes = (int) $request->input('sub_minutes', 0);

        return $timemachineSettings;
    }

    private function getTimemachineSettingsTtl(Request $request)
    {
        return (int) $request->input('ttl', self::DEFAULT_TIMEMACHINE_TTL);
    }
}