<?php

namespace Example;

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPConnectionConfig;
use PhpAmqpLib\Connection\AMQPConnectionFactory;

final class Amqp
{
    // Connects to CLOUDAMQP_URL, e.g. amqps://user:password@host/vhost
    public static function connect(): AbstractConnection
    {
        $url = parse_url(getenv('CLOUDAMQP_URL') ?: 'amqp://guest:guest@localhost');
        $secure = $url['scheme'] === 'amqps';

        $config = new AMQPConnectionConfig();
        $config->setHost($url['host']);
        $config->setPort($url['port'] ?? ($secure ? 5671 : 5672));
        $config->setUser(urldecode($url['user'] ?? 'guest'));
        $config->setPassword(urldecode($url['pass'] ?? 'guest'));
        $config->setVhost(urldecode(ltrim($url['path'] ?? '', '/')) ?: '/');
        $config->setIsSecure($secure);

        return AMQPConnectionFactory::create($config);
    }
}
