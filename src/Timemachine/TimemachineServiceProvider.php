<?php namespace Appkr\Timemachine;

use Carbon\Carbon;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class TimemachineServiceProvider extends ServiceProvider
{
    const LARAVEL = 'LARAVEL';
    const LUMEN = 'LUMEN';
    const TIMEMACHINE_SETTINGS_CACHE_KEY = 'timemachine.settings';

    private $framework;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->detectFramework();
    }

    public function boot()
    {
        $this->publishConfig();
        $this->setCurrentTimeForTest();
    }

    public function register()
    {
        $this->includeRouting();
    }

    private function detectFramework()
    {
        if ($this->app instanceof LaravelApplication) {
            $this->framework = self::LARAVEL;
        } elseif ($this->app instanceof LumenApplication) {
            $this->framework = self::LUMEN;
        }
    }

    private function publishConfig()
    {
        $source = __DIR__ . '/../config/timemachine.php';

        if ($this->framework === self::LARAVEL) {
            $this->publishes([
                $source => config_path('timemachine.php')
            ]);
        } else {
            $this->app->configure('timemachine');
        }

        $this->mergeConfigFrom($source, 'timemachine');
    }

    private function setCurrentTimeForTest()
    {
        $isSafeToRun = $this->app->environment(
            isset($this->app['config']['timemachine.allowed_env'])
                ? $this->app['config']['timemachine.allowed_env']
                : ['local']
        );

        /** @var \Illuminate\Contracts\Cache\Repository $cacheRepository */
        $cacheRepository = $this->app['cache.store'];

        if ($isSafeToRun && $cacheRepository->has(self::TIMEMACHINE_SETTINGS_CACHE_KEY)) {
            $timemachineSettings = $cacheRepository->get(self::TIMEMACHINE_SETTINGS_CACHE_KEY);

            Carbon::setTestNow(
                Carbon::now()
                    ->addDays(isset($timemachineSettings->addDays)
                        ? $timemachineSettings->addDays : 0)
                    ->subDays(isset($timemachineSettings->subDays)
                        ? $timemachineSettings->subDays : 0)
                    ->addMinutes(isset($timemachineSettings->addMinutes)
                        ? $timemachineSettings->addMinutes : 0)
                    ->subMinutes(isset($timemachineSettings->subMinutes)
                        ? $timemachineSettings->subMinutes : 0)
            );
        }
    }

    private function includeRouting()
    {
        if ($this->framework === self::LARAVEL) {
            include __DIR__ . '/../routes/laravel.php';
        } else {
            $app = $this->app;
            include __DIR__ . '/../routes/lumen.php';
        }
    }
}

