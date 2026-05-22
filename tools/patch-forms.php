<?php

$files = [
    'resources/views/admin/members/_form.blade.php',
    'resources/views/admin/activities/_form.blade.php',
    'resources/views/admin/announcements/_form.blade.php',
    'resources/views/admin/dues/_form.blade.php',
];

$base = dirname(__DIR__);
foreach ($files as $f) {
    $path = $base.'/'.$f;
    if (! is_file($path)) {
        continue;
    }
    $c = file_get_contents($path);
    $c = preg_replace('/class="w-full rounded(-lg)? border[^"]*text-sm"/', 'class="input-touch"', $c);
    $c = preg_replace('/class="w-full rounded border[^"]*text-sm"/', 'class="input-touch"', $c);
    $c = str_replace('rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white', 'btn-primary', $c);
    $c = str_replace('rounded border px-4 py-2 text-sm', 'btn-secondary', $c);
    file_put_contents($path, $c);
}

$dash = $base.'/resources/views/admin/dashboard.blade.php';
$c = file_get_contents($dash);
$c = str_replace('grid gap-4 sm:grid-cols-2 lg:grid-cols-3', 'grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3', $c);
$c = str_replace('rounded-xl border bg-white p-6 shadow-sm', 'card p-4 sm:p-6', $c);
file_put_contents($dash, $c);

echo "forms patched\n";
