<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mods = Module::toCollection()->toArray();
foreach ($mods as $m) {
    $name = $m['name'];
    $prop = App\System::getProperty(strtolower($name) . '_version');
    echo $name . ': ' . ($prop === null ? '[MISSING]' : $prop) . PHP_EOL;
}
