<?php
require('vendor/autoload.php');
define('AMQP_DEBUG', false);
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Message\AMQPMessage;

$url_str = getenv('CLOUDAMQP_URL')
  or exit("CLOUDAMQP_URL not set");
$url = parse_url($url_str);
$vhost = ($url['path'] == '/' || !isset($url['path'])) ? '/' : substr($url['path'], 1);
$port = $url['port'];
if($url['scheme'] === "amqps") {
    $port = isset($port) ? $port : 5671;
    $ssl_opts = array(
        'capath' => '/etc/ssl/certs'
    );
    $conn = new AMQPSSLConnection($url['host'], $port, $url['user'], $url['pass'], $vhost, $ssl_opts);
} else {
    $port = isset($port) ? $port : 5672;
    $conn = new AMQPStreamConnection($url['host'], $port, $url['user'], $url['pass'], $vhost);
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
