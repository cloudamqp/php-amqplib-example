<?php
require('vendor/autoload.php');

use Enqueue\AmqpBunny\AmqpConnectionFactory;
use Interop\Amqp\AmqpMessage;
use Interop\Amqp\AmqpQueue;

$context = (new AmqpConnectionFactory(getenv('CLOUDAMQP_URL')))->createContext();
$queue = $context->createQueue('basic_get_queue');
$queue->addFlag(AmqpQueue::FLAG_DURABLE);

$message = $context->createMessage('the body');
$message->setContentType('text/plain');
$message->setDeliveryMode(AmqpMessage::DELIVERY_MODE_PERSISTENT);

$context->createProducer()->send($queue, $message);

$consumer = $context->createConsumer($queue);
$gotMessage = $consumer->receiveNoWait();
var_dump($gotMessage->getBody());

$consumer->acknowledge($gotMessage);

$context->close();
