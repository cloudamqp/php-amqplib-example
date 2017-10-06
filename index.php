<?php
require('vendor/autoload.php');
define('AMQP_DEBUG', false);
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Message\AMQPMessage;

$url = parse_url(getenv('CLOUDAMQP_URL'));
$vhost = substr($url['path'], 1);
if($url['scheme'] === "amqps") {
    $ssl_opts = array(
        'capath' => '/etc/ssl/certs'
    );
    $conn = new AMQPSSLConnection($url['host'], 5671, $url['user'], $url['pass'], $vhost, $ssl_opts);    
} else {
    $conn = new AMQPStreamConnection($url['host'], 5672, $url['user'], $url['pass'], $vhost);    
}

$ch = $conn->channel();

$exchange = 'amq.direct';
$queue = 'basic_get_queue';
$ch->queue_declare($queue, false, true, false, false);
$ch->exchange_declare($exchange, 'direct', true, true, false);
$ch->queue_bind($queue, $exchange);

$msg_body = 'the body';
$msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
echo "Sending message...\n";
$ch->basic_publish($msg, $exchange);


$retrived_msg = $ch->basic_get($queue);
echo sprintf("Message recieved: %s\n", $retrived_msg->body);
$ch->basic_ack($retrived_msg->delivery_info['delivery_tag']);

$ch->close();
$conn->close();
?>
