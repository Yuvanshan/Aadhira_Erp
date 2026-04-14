<?php

namespace Modules\Crm\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Routing\Router;

class CrmServiceProvider extends ServiceProvider
{
    protected $middleware = [
        'Crm' => [
            'ContactSidebarMenu' => 'ContactSidebarMenu',
            'CheckContactLogin' => 'CheckContactLogin'
        ],
    ];

    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->registerMiddleware($this->app['router']);
        $this->registerViewComposers();
        $this->registerScheduleCommands();
    }

    /**
     * SAFE VIEW COMPOSER (NO CRASH DURING BUILD)
     */
    protected function registerViewComposers()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        View::composer(['crm::layouts.nav'], function ($view) {

            $commonUtil = new Util();

            try {
                if (auth()->check()) {
                    $is_admin = $commonUtil->is_admin(
                        auth()->user(),
                        auth()->user()->business_id
                    );

                    $view->with('__is_admin', $is_admin);
                } else {
                    $view->with('__is_admin', false);
                }
            } catch (\Exception $e) {
                $view->with('__is_admin', false);
            }
        });
    }

    /**
     * SAFE SCHEDULER REGISTRATION (NO DB CALL DURING BOOT)
     */
    public function registerScheduleCommands()
    {
        $this->app->booted(function () {

            try {
                // Prevent build-time execution
                if (app()->runningInConsole() && !app()->environment('local')) {
                    return;
                }

                if (!Schema::hasTable('system')) {
                    return;
                }

                $moduleUtil = app(ModuleUtil::class);

                $is_installed = $moduleUtil->isModuleInstalled(config('crm.name'));

                if ($is_installed) {
                    $schedule = $this->app->make(Schedule::class);

                    $schedule->command('pos:sendScheduleNotification')->everyMinute();
                    $schedule->command('pos:createRecursiveFollowup')->daily();
                }

            } catch (\Exception $e) {
                // silently fail during build
            }
        });
    }

    public function registerMiddleware(Router $router)
    {
        foreach ($this->middleware as $module => $middlewares) {
            foreach ($middlewares as $name => $middleware) {
                $class = "Modules\\{$module}\\Http\\Middleware\\{$middleware}";
                $router->aliasMiddleware($name, $class);
            }
        }
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->registerCommands();
    }

    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('crm.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'crm'
        );
    }

    public function registerViews()
    {
        $viewPath = resource_path('views/modules/crm');
        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/crm';
        }, config('view.paths')), [$sourcePath]), 'crm');
    }

    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/crm');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'crm');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'crm');
        }
    }

    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    public function provides()
    {
        return [];
    }

    protected function registerCommands()
    {
        $this->commands([
            \Modules\Crm\Console\SendScheduleNotification::class,
            \Modules\Crm\Console\CreateRecursiveFollowup::class,
        ]);
    }
}