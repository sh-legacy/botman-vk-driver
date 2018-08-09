<?php

namespace BotMan\Drivers\Vk\Providers;

use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Vk\VkDriver;
use BotMan\Studio\Providers\StudioServiceProvider;
use Illuminate\Support\ServiceProvider;

class VkServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->isRunningInBotManStudio()) {
            $this->loadDrivers();
            $this->publishes([
                __DIR__.'/../../stubs/vk.php' => config_path('botman/vk.php'),
            ]);
            $this->mergeConfigFrom(__DIR__.'/../../stubs/vk.php', 'botman.vk');
        }
    }
    /**
     * Load BotMan drivers.
     */
    protected function loadDrivers()
    {
        DriverManager::loadDriver(VkDriver::class);
    }
    /**
     * @return bool
     */
    protected function isRunningInBotManStudio()
    {
        return class_exists(StudioServiceProvider::class);
    }
}