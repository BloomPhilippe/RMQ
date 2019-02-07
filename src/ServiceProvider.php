<?php
namespace BloomPhilippe\RMQ;

use Illuminate\Support\ServiceProvider as BaseProvider;
use BloomPhilippe\RMQ\Console\RMQListenCommand;
use BloomPhilippe\RMQ\Console\RMQRpcServerCommand;
use BloomPhilippe\RMQ\Services\RMQService;

class ServiceProvider extends BaseProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('rmqService', function($app){
            return new RMQService();
        });

        $this->app->singleton('command.rmq.listen', function () {
            return new RMQListenCommand();
        });

        $this->app->singleton('command.rmq.rpc', function () {
            return new RMQRpcServerCommand();
        });

        $this->commands(
            'command.rmq.listen',
            'command.rmq.rpc'
        );

    }

}
