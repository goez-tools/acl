<?php

namespace Goez\Acl;

use Illuminate\Support\ServiceProvider;

class AclServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * 引導包服務
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('acl.php')
        ], 'acl-config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('acl', function ($app) {
            $user = $app['auth']->user();
            $acl = new Acl($user);
            $fn = $app['config']->get('acl::init', null);

            if ($fn) {
                $fn($acl);
            }

            return $acl;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['acl'];
    }
}
