<?php

namespace BloomPhilippe\RMQ\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Log;

define('SOCKET_EAGAIN', '');

class RMQService
{

    protected $connection;
    protected $channel;
    protected $queue;
    protected $response;
    protected $corr_id;
    protected $callback_queue;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(env('RABBITMQ_HOST', 'localhost'), env('RABBITMQ_PORT', '5672'), env('RABBITMQ_LOGIN', 'guest'), env('RABBITMQ_PASSWORD', 'guest'));

        $this->channel = $this->connection->channel();
    }


    public function on_response($rep)
    {
        if ($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }


    public function callRpc($body, $exchange = '', $queue)
    {

        list($this->callback_queue, ,) = $this->channel->queue_declare("", false, false, true, false);

        $this->channel->queue_declare($queue, false, true, false, false);

        $this->channel->basic_consume($this->callback_queue, '', false, false, false, false, array($this, 'on_response'));

        $this->response = null;
        $this->corr_id = uniqid();

        $msg = new AMQPMessage(
            $body,
            array(
                'correlation_id' => $this->corr_id,
                'reply_to' => $this->callback_queue
            )
        );

        try {
            $this->channel->basic_publish($msg, $exchange);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        while (!$this->response) {
            $this->channel->wait();
        }
        return $this->response;
    }


    public function receiveRpcCallback(){
        
        $body = 'Message received';

        try {
            $msg = new AMQPMessage(
                $body,
                array(
                    'correlation_id' => $response->get('correlation_id')
                )
            );

            $response->delivery_info['channel']->basic_publish(
                $msg,
                '',
                $response->get('reply_to')
            );

            $response->delivery_info['channel']->basic_ack(
                $response->delivery_info['delivery_tag']
            );


        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @param $queue
     */
    public function receiveRpc($queue)
    {

        $this->channel->basic_qos(null, 1, null);

        $this->channel->basic_consume($queue, '', false, false, false, false, [$this, 'receiveRpcCallback']);

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    /**
     * @param $body
     * @param string $exchange
     */
    public function call($body, $exchange = '', $queue)
    {
        $msg = new AMQPMessage(
            $body
        );

        try {
            $this->channel->basic_publish($msg, $exchange, $queue);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function listenCallback(AMQPMessage $response){
        Log::error('You did not extend RMQService, read the readme');
    }

    public function listen($queue)
    {
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($queue, '', false, true, false, false, [$this, 'listenCallback']);
        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

}