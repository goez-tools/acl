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
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->package('goez/acl');

        $this->app['acl'] = $this->app->share(function ($app) {
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
    public function provides()
    {
        return array('acl');
    }

}
