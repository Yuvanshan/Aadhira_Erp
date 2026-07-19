<?php

namespace App\Console\Commands;

use App\Business;
use App\System;
use App\Utils\ModuleUtil;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Module;

class FixInstalledModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:fix-modules {--migrate : Run module migrations for restored modules} {--publish : Publish module assets for restored modules} {--enable-all-businesses : Enable all available modules for every business}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore missing module install markers and optionally migrate/publish restored modules. Can also enable all available modules for businesses.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $modules = Module::toCollection()->toArray();
        $restored = [];
        $skipped = [];
        $failed = [];

        foreach ($modules as $module) {
            $module_name = $module['name'];

            if (! Module::has($module_name)) {
                $skipped[] = $module_name;
                continue;
            }

            $system_key = strtolower($module_name). '_version';
            $module_version = System::getProperty($system_key);

            if (! empty($module_version)) {
                $skipped[] = $module_name;
                continue;
            }

            $config_key = strtolower($module_name) . '.module_version';
            $config_version = config($config_key);

            if (empty($config_version)) {
                $failed[] = $module_name;
                continue;
            }

            try {
                System::addProperty($system_key, $config_version);
                $this->info("Restored module version for {$module_name} => {$config_version}");
                $restored[] = $module_name;

                if ($this->option('migrate')) {
                    Artisan::call('module:migrate', ['module' => $module_name]);
                    $this->info("  Migrated {$module_name}");
                }

                if ($this->option('publish')) {
                    Artisan::call('module:publish', ['module' => $module_name, '--force' => true]);
                    $this->info("  Published assets for {$module_name}");
                }
            } catch (\Exception $e) {
                $failed[] = $module_name;
                $this->error("Failed to restore {$module_name}: {$e->getMessage()}");
            }
        }

        $this->line('');
        $this->info('Module restore summary:');
        $this->info('  Restored: ' . count($restored));
        $this->info('  Skipped (already present or unavailable): ' . count($skipped));
        $this->info('  Failed: ' . count($failed));

        if (! empty($failed)) {
            $this->error('Failed modules: ' . implode(', ', $failed));
        }

        if ($this->option('enable-all-businesses')) {
            $module_util = new ModuleUtil();
            $available_modules = array_keys($module_util->availableModules());

            $businesses = Business::all();
            foreach ($businesses as $business) {
                $business->enabled_modules = $available_modules;
                $business->save();
                $this->info('Enabled all modules for business: ' . $business->name);
            }

            $this->line('');
            $this->info('Enabled all available modules for ' . $businesses->count() . ' businesses.');
        }

        return 0;
    }
}
