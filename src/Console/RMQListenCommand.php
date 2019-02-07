<?php

namespace BloomPhilippe\RMQ\Console;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RMQListenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rmq:listen {queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'RMQ Listen';

    /**
     * RMQListenCommand constructor.
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
        app('rmqService')->listen($queue);
    }
}
