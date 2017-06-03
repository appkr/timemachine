<?php

namespace Appkr\Timemachine\Tests;

use Appkr\Timemachine\TimemachineSettings;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase;
use Mockery;

class TimemachineTest extends TestCase
{
    const TIMEMACHINE_SETTINGS_CACHE_KEY = 'timemachine.settings';
    const REGEXP_ISO8601 = '/(\d{4})-(\d{2})-(\d{2})T(\d{2})\:(\d{2})\:(\d{2})[+-](\d{2})\:(\d{2})/';

    public function createApplication()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $app->register(\Appkr\Timemachine\TimemachineServiceProvider::class);

        return $app;
    }

    public function test_get_time_diff_api_responds_current_server_time()
    {
        $response = $this->get('timemachine')
            ->assertStatus(200)
            ->decodeResponseJson();

        $this->assertRegExp(self::REGEXP_ISO8601, $response['current_server_time']);
        $this->assertTrue($response['current_server_time'] === $response['target_server_time']);
    }

    public function test_get_time_diff_api_responds_future_target_server_time()
    {
        $futureTimeString = Carbon::now()->addMinutes(5)->toDateTimeString();

        $response = $this->get("timemachine?target_server_time={$futureTimeString}")
            ->assertStatus(200)
            ->decodeResponseJson();

        $this->assertTrue($response['add_days'] === 0);
        $this->assertTrue($response['add_minutes'] === 5);
        $this->assertTrue($response['sub_days'] === null);
        $this->assertTrue($response['sub_minutes'] === null);
    }

    public function test_get_time_diff_api_responds_past_target_server_time()
    {
        $pastTimeString = Carbon::now()->subMinutes(5)->toDateTimeString();

        $response = $this->get("timemachine?target_server_time={$pastTimeString}")
            ->assertStatus(200)
            ->decodeResponseJson();

        $this->assertTrue($response['add_days'] === null);
        $this->assertTrue($response['add_minutes'] === null);
        $this->assertTrue($response['sub_days'] === 0);
        $this->assertTrue($response['sub_minutes'] === 5);
    }

    public function test_set_current_time_api_sets_future_time()
    {
        $this->markTestIncomplete('TODO: Find how to test');
        $futureTimeString = Carbon::now()->addMinutes(5)->toIso8601String();

        $response = $this->get('timemachine')
            ->assertStatus(200)
            ->decodeResponseJson();

        $this->assertTrue($response['current_server_time'] === $futureTimeString);
    }

    /** @setUp */
    public function mockCache()
    {
        $timemachineSettings = new TimemachineSettings;
        $timemachineSettings->addMinutes = 5;

        $m = Mockery::mock(\Illuminate\Contracts\Cache\Repository::class);

        $m->shouldReceive('has')
            ->with(self::TIMEMACHINE_SETTINGS_CACHE_KEY)
            ->andReturn(true);
        $m->shouldReceive('get')
            ->with(self::TIMEMACHINE_SETTINGS_CACHE_KEY)
            ->andReturn($timemachineSettings);
    }
}