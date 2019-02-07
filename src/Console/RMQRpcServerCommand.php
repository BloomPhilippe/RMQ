<?php

namespace BloomPhilippe\RMQ\Console;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RMQRpcServerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rmq:rpc {queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen RMQ RPC';

    /**
     * RMQRpcServerCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $queue = $this->argument('queue');
        app('rmqService')->receiveRpc($queue);
    }
}
