<?php

namespace Appkr\Timemachine;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class TimemachineController extends Controller
{
    const TIMEMACHINE_SETTINGS_CACHE_KEY = 'timemachine.settings';

    public function getTimeDiff(GetTimeDiffRequest $request)
    {
        $targetTime = $request->getTargetTime();
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

    public function setCurrentTime(UpdateCurrentTimeRequest $request)
    {
        /** @var \Illuminate\Contracts\Cache\Repository $cacheRepository */
        $cacheRepository = app('cache');
        $timemachineSettingsTtl = $request->getTimemachineSettingsTtl();

        $cacheRepository->put(
            self::TIMEMACHINE_SETTINGS_CACHE_KEY,
            $request->getTimemachineSettingsDto(),
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
        $cacheRepository = app('cache');
        $cacheRepository->forget(self::TIMEMACHINE_SETTINGS_CACHE_KEY);

        return response()->json([
            'current_server_time' => Carbon::now()->toIso8601String(),
            'message' => 'Success. Settings removed.',
        ]);
    }
}