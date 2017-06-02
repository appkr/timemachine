<?php namespace Appkr\Timemachine;

use Carbon\Carbon;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class TimemachineServiceProvider extends ServiceProvider
{
    const TIMEMACHINE_SETTINGS_CACHE_KEY = 'timemachine.settings';

    public function boot()
    {
        $isSafeToRun = $this->app->environment(['local', 'dev']);

        if ($isSafeToRun) {
            $this->setCurrentTimeForTest();
        }
    }

    public function register()
    {
        if ($this->app instanceof LaravelApplication) {
            include __DIR__ . '/../routes/laravel.php';
        } elseif ($this->app instanceof LumenApplication) {
            $app = $this->app;
            include __DIR__ . '/../routes/lumen.php';
        }
    }

    private function setCurrentTimeForTest()
    {
        /** @var \Illuminate\Contracts\Cache\Repository $cacheRepository */
        $cacheRepository = $this->app['cache.store'];

        if ($cacheRepository->has(self::TIMEMACHINE_SETTINGS_CACHE_KEY)) {
            $timemachineSettings = $cacheRepository->get(self::TIMEMACHINE_SETTINGS_CACHE_KEY);

            Carbon::setTestNow(
                Carbon::now()
                    ->addDays(isset($timemachineSettings->addDays) ? $timemachineSettings->addDays : 0)
                    ->subDays(isset($timemachineSettings->subDays) ? $timemachineSettings->subDays : 0)
                    ->addMinutes(isset($timemachineSettings->addMinutes) ? $timemachineSettings->addMinutes : 0)
                    ->subMinutes(isset($timemachineSettings->subMinutes) ? $timemachineSettings->subMinutes : 0)
            );
        }
    }
}

