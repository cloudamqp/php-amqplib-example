<?php

require __DIR__ . '/vendor/autoload.php';

use Example\Amqp;
use Example\Worker;

$connection = Amqp::connect();
$channel = $connection->channel();
Worker::consume($channel, 'hello', function (string $message) {
    echo " [x] Received '$message'\n";
});
echo " [*] Waiting for messages. To exit press CTRL+C\n";
$channel->consume();
