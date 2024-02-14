<?php

namespace Libraries;

use Enqueue\AmqpLib\AmqpConnectionFactory;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * MessageBroker
 */
class MessageBroker
{
    /**
     * sendMessage
     * @param string $event
     * @param array $data
     */
    public static function sendMessage(string $event, array $data)
    {
        $mbroker_ip = getenv('HOST_MESSAGEBROKER');
        $mbroker_user = getenv('USER_MESSAGEBROKER');
        $mbroker_pass = getenv('PASS_MESSAGEBROKER');
        $connectionFactory = new AmqpConnectionFactory(
            'amqp://' . $mbroker_user . ':' . $mbroker_pass . '@' . $mbroker_ip . ':5672'
        );
        $context = $connectionFactory->createContext();
        $topic = $context->createTopic($event);
        $producer = $context->createProducer();
        $message = $context->createMessage(json_encode($data));
        $producer->send($topic, $message);
    }

    /**
     * processMessage
     * @param string $event
     * @param string $name_func
     */
    public static function processMessage(string $event, string $name_func)
    {
        $mbroker_ip = getenv('HOST_MESSAGEBROKER');
        $mbroker_user = getenv('USER_MESSAGEBROKER');
        $mbroker_pass = getenv('PASS_MESSAGEBROKER');
        $connection = new AMQPStreamConnection($mbroker_ip, 5672, $mbroker_user, $mbroker_pass, '/');
        $channel = $connection->channel();
        $channel->exchange_declare(
            $event,
            'direct',
            false,
            true,
            false
        );
        $queueName = $event . 'Queue';
        $channel->queue_declare(
            $queueName,
            false,
            true,
            false,
            false
        );
        $channel->queue_bind($queueName, $event);
        $callback = function (AMQPMessage $message) use ($name_func) {
            $body = json_decode($message->getBody(), true);
            call_user_func($name_func, $body);
        };
        $channel->basic_consume($queueName, '', false, true, false, false, $callback);
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
