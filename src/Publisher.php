<?php

namespace Example;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

final class Publisher
{
    // Waits until the broker has confirmed the message
    public static function publish(AMQPChannel $channel, string $queue, string $message): void
    {
        $channel->queue_declare($queue, false, true, false, false);
        $channel->confirm_select();
        $channel->basic_publish(new AMQPMessage($message, [
            'content_type' => 'text/plain',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        ]), '', $queue);
        $channel->wait_for_pending_acks(5);
        echo " [x] Sent '$message'\n";
    }
}
