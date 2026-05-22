<?php

namespace App\Providers;

use App\Models\Organization;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('local') && ! $this->app->runningInConsole()) {
            $request = $this->app->make('request');
            $host = $request->getHost();

            if (! $this->isLoopbackHost($host)) {
                URL::forceRootUrl($request->getSchemeAndHttpHost());
            }
        }

        View::composer(['layouts.admin', 'layouts.member', 'layouts.guest', 'auth.login'], function ($view) {
            $view->with('organization', Organization::first());
        });

        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp '.number_format($expression, 0, ',', '.'); ?>";
        });
    }

    private function isLoopbackHost(string $host): bool
    {
        return in_array($host, ['localhost', '127.0.0.1', '[::1]'], true)
            || str_starts_with($host, '127.');
    }
}
