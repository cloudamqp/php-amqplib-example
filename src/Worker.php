<?php

namespace Example;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

final class Worker
{
    // The message is acked after the handler has processed it
    public static function consume(AMQPChannel $channel, string $queue, callable $handler): void
    {
        $channel->queue_declare($queue, false, true, false, false);
        $channel->basic_qos(0, 10, false);
        $channel->basic_consume($queue, '', false, false, false, false, function (AMQPMessage $msg) use ($handler) {
            $handler($msg->getBody());
            $msg->ack();
        });
    }
}
