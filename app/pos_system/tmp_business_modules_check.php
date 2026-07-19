<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$businesses = App\Business::all();
foreach ($businesses as $business) {
    echo 'Business ID ' . $business->id . ': ' . $business->name . PHP_EOL;
    echo 'Raw enabled_modules: ' . json_encode($business->getOriginal('enabled_modules')) . PHP_EOL;
    echo 'Casted enabled_modules: ' . json_encode($business->enabled_modules) . PHP_EOL;
    echo '---' . PHP_EOL;
}
