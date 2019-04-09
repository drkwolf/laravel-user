<?php namespace drkwolf\Larauser;

/**
 *
 * @package drkwolf\Larauser
 */
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class LarauserServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'Migration' => 'command.larauser.migration',
    ];

    /**
     * The middlewares to be registered.
     *
     * @var array
     */
    protected $middlewares = [
        // 'role' => \Laratrust\Middleware\LaratrustRole::class,
        // 'permission' => \Laratrust\Middleware\LaratrustPermission::class,
        // 'ability' => \Laratrust\Middleware\LaratrustAbility::class,
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        $this->mergeConfigFrom(__DIR__.'/../config/larauser.php', 'larauser');

        $this->publishes([
            __DIR__.'/../config/larauser.php' => config_path('larauser.php'),
        ], 'config');

        // $this->registerMiddlewares();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // $this->registerLarauser();

        // $this->registerCommands();
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerLarauser()
    {
        $this->app->bind('laratrust', function ($app) {
            return new Laratrust($app);
        });

        $this->app->alias('laratrust', 'Laratrust\Laratrust');
    }

    /**
     * Register the given commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        foreach (array_keys($this->commands) as $command) {
            $method = "register{$command}Command";

            call_user_func_array([$this, $method], []);
        }

        $this->commands(array_values($this->commands));
    }

    protected function registerMigrationCommand()
    {
        $this->app->singleton('command.laratrust.migration', function () {
            return new \Laratrust\Commands\MigrationCommand();
        });
    }

    /**
     * Get the services provided.
     *
     * @return array
     */
    public function provides()
    {
        return array_values($this->commands);
    }
}
