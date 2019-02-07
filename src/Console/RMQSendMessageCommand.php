<?php

namespace BloomPhilippe\RMQ\Console;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RMQSendMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rmq:send {queue} {msg}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'RMQ send message';

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
        app('rmqService')->call($this->argument('queue'), '', $this->argument('msg'));
    }
}
