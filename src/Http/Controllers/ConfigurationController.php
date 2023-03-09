<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Team64j\LaravelEvolution\Models\SystemSetting;
use Team64j\LaravelManager\Http\Requests\ConfigurationRequest;
use Team64j\LaravelManager\Http\Resources\ConfigurationResource;
use Team64j\LaravelManager\Layouts\ConfigurationLayout;

class ConfigurationController extends Controller
{
    /**
     * @param ConfigurationRequest $request
     * @param ConfigurationLayout $layout
     *
     * @return ConfigurationResource
     */
    public function index(ConfigurationRequest $request, ConfigurationLayout $layout): ConfigurationResource
    {
        return (new ConfigurationResource(SystemSetting::all()->pluck('setting_value', 'setting_name')))
            ->additional([
                'layout' => $layout->default(),
            ]);
    }

    /**
     * @param ConfigurationRequest $request
     * @param SystemSetting $configuration
     * @param ConfigurationLayout $layout
     *
     * @return ConfigurationResource
     */
    public function store(
        ConfigurationRequest $request,
        SystemSetting $configuration,
        ConfigurationLayout $layout): ConfigurationResource
    {
        $data = [];

        foreach ($request->all() as $key => $value) {
            $data[] = [
                'setting_name' => $key,
                'setting_value' => $value,
            ];
        }

        $configuration->upsert($data, 'setting_name');

        Cache::clear();

        Artisan::call('optimize:clear');

        return new ConfigurationResource([
            'redirect' => route('manager.dashboard'),
        ]);
    }
}
