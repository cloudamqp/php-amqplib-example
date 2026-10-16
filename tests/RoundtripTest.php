<?php

use Example\Amqp;
use Example\Publisher;
use Example\Worker;
use PHPUnit\Framework\TestCase;

final class RoundtripTest extends TestCase
{
    public function testWorkerReceivesPublishedMessage(): void
    {
        $queue = 'roundtrip-' . bin2hex(random_bytes(8));
        $received = null;

        $connection = Amqp::connect();
        $consumerChannel = $connection->channel();
        Worker::consume($consumerChannel, $queue, function (string $message) use (&$received) {
            $received = $message;
        });

        Publisher::publish($connection->channel(), $queue, 'Hello CloudAMQP!');
        $consumerChannel->wait(null, false, 5);

        $this->assertSame('Hello CloudAMQP!', $received);
        $consumerChannel->queue_delete($queue);
        $connection->close();
    }
}
