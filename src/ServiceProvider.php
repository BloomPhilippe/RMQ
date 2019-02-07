<?php
namespace BloomPhilippe\RMQ;

use Illuminate\Support\ServiceProvider as BaseProvider;
use BloomPhilippe\RMQ\Console\RabbitMQListenCommand;
use BloomPhilippe\RMQ\Services\RabbitMQManage;

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

        $this->commands(
            'command.rmq.listen'
        );

    }

}
