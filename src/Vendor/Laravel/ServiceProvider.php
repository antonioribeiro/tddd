<?php namespace PragmaRX\Ci\Vendor\Laravel;
 
use PragmaRX\Ci\Ci;

use PragmaRX\Ci\Support\Config;
use PragmaRX\Ci\Support\FileSystem;

use PragmaRX\Ci\Data\Repositories\RepositoryExample;

use PragmaRX\Ci\Data\RepositoryManager;

use PragmaRX\Ci\Vendor\Laravel\Models\ModelExample;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Illuminate\Foundation\AliasLoader as IlluminateAliasLoader;

class ServiceProvider extends IlluminateServiceProvider {

    const PACKAGE_NAMESPACE = 'pragmarx/ci';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package(self::PACKAGE_NAMESPACE, self::PACKAGE_NAMESPACE, __DIR__.'/../..');

        if( $this->app['config']->get(self::PACKAGE_NAMESPACE.'::create_ci_alias') )
        {
            IlluminateAliasLoader::getInstance()->alias(
                                                            $this->getConfig('ci_alias'),
                                                            'PragmaRX\Ci\Vendor\Laravel\Facade'
                                                        );
        }

        $this->wakeUp();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {   
        $this->registerConfig();

        $this->registerRepositories();

        $this->registerCi();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('ci');
    }

    /**
     * Takes all the components of Ci and glues them
     * together to create Ci.
     *
     * @return void
     */
    private function registerCi()
    {
        $this->app['ci'] = $this->app->share(function($app)
        {
            $app['ci.loaded'] = true;

            return new Ci(
                                    $app['ci.config'],
                                    $app['ci.repository.manager']
                                );
        });
    }

    public function registerRepositories()
    {
        $this->app['ci.repository.manager'] = $this->app->share(function($app)
        {
            return new RepositoryManager(
                                            $app['ci.config'],
                                            new RepositoryExample(new ModelExample)
                                        );
        });
    }

    public function registerConfig()
    {
        $this->app['ci.config'] = $this->app->share(function($app)
        {
            return new Config($app['config'], self::PACKAGE_NAMESPACE);
        });
    }

    private function wakeUp()
    {
        $this->app['ci']->boot();
    }

    private function getConfig($key)
    {
        return $this->app['config']->get(self::PACKAGE_NAMESPACE.'::'.$key);
    }

}
